<?php

namespace App\Jobs\Diagnostic;

use App\Models\SubIdx;
use App\Models\SubIdxMeta;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CollectSubIdxMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 30;

    protected $subIdx;

    public function __construct(SubIdx $subIdx)
    {
        $this->subIdx = $subIdx;
    }

    public function handle()
    {
        if($this->subIdx->meta !== null) {
            \Log::error("Tried running a CollectSubIdxMetaJob for a SubIdx that already has meta info ({$this->subIdx->id})");
            return;
        }

        $dirPath = storage_disk_file_path($this->subIdx->store_directory);
        $subFilePath = $this->subIdx->filePathWithoutExtension . '.sub';
        $idxFilePath = $this->subIdx->filePathWithoutExtension . '.idx';

        if(!file_exists($subFilePath) || !file_exists($idxFilePath)) {
            \Log::error("CollectSubIdxMetaJob: sub or idx file does not exist ({$this->subIdx->id})");
            return;
        }

        $allSuccessful = true;

        foreach($this->subIdx->languages as $language) {
            if(!$language->hasFinished) {
                \Log::error("CollectSubIdxMetaJob: not all languages are finished ({$this->subIdx->id})");
                return;
            }

            if($language->output_stored_file_id === null) {
                $allSuccessful = false;
            }
        }

        $meta = new SubIdxMeta();

        $meta->sub_idx_id = $this->subIdx->id;

        $meta->sub_file_size = filesize($subFilePath);
        $meta->idx_file_size = filesize($idxFilePath);

        $meta->all_successful = $allSuccessful;

        if($allSuccessful) {
            unlink($subFilePath);
            unlink($idxFilePath);

            $maybeLeftOverSrtFilePath = $this->subIdx->filePathWithoutExtension . '.srt';

            if(file_exists($maybeLeftOverSrtFilePath)) {
                unlink($maybeLeftOverSrtFilePath);
            }

            rmdir($dirPath);

            $meta->deleted = true;
        }
        else {
            $meta->deleted = false;
        }

        $meta->save();
    }

    public function failed(Exception $exception)
    {
        \Log::error("Failed collecting subidx meta for {$this->subIdx->id}");
        \Log::error($exception->getMessage());
    }
}
