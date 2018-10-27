<?php

namespace App\Models;

use App\Jobs\Sup\ExtractSupImagesJob;
use App\Models\Diagnostic\SupJobMeta;
use App\Models\Traits\MeasuresQueueTime;
use Illuminate\Database\Eloquent\Model;

class SupJob extends Model
{
    use MeasuresQueueTime;

    protected $guarded = [];

    protected $casts = [
        'input_stored_file_id' => 'int',
        'output_stored_file_id' => 'int',
        'last_cache_hit' => 'datetime',
        'cache_hits' => 'int',
    ];

    public function meta()
    {
        return $this->hasOne(SupJobMeta::class);
    }

    public function inputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'input_stored_file_id');
    }

    public function outputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function getIsFinishedAttribute()
    {
        return $this->finished_at !== null;
    }

    public function getHasErrorAttribute()
    {
        return $this->error_message !== null;
    }

    /**
     * Dispatch the job to turn this sup into an srt
     */
    public function dispatchJob()
    {
        ExtractSupImagesJob::dispatch($this)->onQueue('larry-default');
    }

    /**
     * Reset and retry this sup job
     */
    public function retry()
    {
        if ($this->inputStoredFile === null) {
            abort(422, 'Can not retry sup job, input file has been deleted');
        }

        $this->update([
            'created_at'             => now(),
            'updated_at'             => now(),
            'output_stored_file_id'  => null,
            'error_message'          => null,
            'internal_error_message' => null,
            'temp_dir'               => null,
            'started_at'             => null,
            'finished_at'            => null,
            'queue_time'             => null,
            'extract_time'           => null,
            'work_time'              => null,
        ]);

        optional($this->meta)->delete();

        $this->dispatchJob();
    }
}
