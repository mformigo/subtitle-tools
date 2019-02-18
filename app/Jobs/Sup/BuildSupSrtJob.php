<?php

namespace App\Jobs\Sup;

use App\Events\SupJobChanged;
use App\Jobs\BaseJob;
use App\Models\StoredFile;
use App\Models\SupJob;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\SrtCue;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use SjorsO\Sup\SupFile;

class BuildSupSrtJob extends BaseJob implements ShouldQueue
{
    public $timeout = 30;

    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        $cuesText = $this->collectTextByIndex($this->supJob->temp_dir);

        $cueManifest = SupFile::open($this->supJob->inputStoredFile->file_path)->getCueManifest();

        $srt = new Srt();

        foreach ($cueManifest as $cue) {
            $textArray = $cuesText[$cue['index']];

            $startMs = $cue['startTime'];
            $endMs   = $cue['endTime'];

            if ($endMs < $startMs) {
                $endMs = $startMs;
            }

            $cue = (new SrtCue)
                ->setLines($textArray)
                ->setTiming($startMs, $endMs);

            $srt->addCue($cue);
        }

        $srt->removeEmptyCues();

        if (! $srt->hasCues()) {
            return $this->failed(null, 'messages.sup.no_cues_with_dialogue');
        }

        $this->supJob->output_stored_file_id = StoredFile::createFromTextFile($srt)->id;

        return $this->endJob();
    }

    protected function collectTextByIndex($directory)
    {
        return collect(scandir($directory))
            ->filter(function ($fileName) {
                return ends_with($fileName, '.txt');
            })
            ->keyBy(function ($filePath) {
                preg_match('/\[(\d+)-\d+\]/', $filePath, $match);

                return (int)$match[1];
            })
            ->map(function ($fileName) use ($directory) {
                return file(str_finish($directory, '/').$fileName);
            })
            ->all();
    }

    public function failed($e, $errorMessage = null)
    {
        $this->supJob->error_message = $errorMessage ?: 'messages.sup.job_failed';

        $this->supJob->internal_error_message = ($e instanceof Exception) ? $e->getMessage() : $e;

        return $this->endJob();
    }

    protected function endJob()
    {
        $startedAt = new Carbon($this->supJob->started_at);

        $this->supJob->finished_at = $finishedAt = now();

        $this->supJob->work_time = $finishedAt->diffInSeconds($startedAt);

        $this->supJob->save();

        SupJobChanged::dispatch($this->supJob);

        if (is_dir($this->supJob->temp_dir)) {
            $globPattern = str_finish($this->supJob->temp_dir, '/').'*';

            foreach (glob($globPattern) as $filePath) {
                unlink($filePath);
            }

            rmdir($this->supJob->temp_dir);
        }

        return $this->supJob;
    }
}
