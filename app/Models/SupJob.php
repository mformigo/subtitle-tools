<?php

namespace App\Models;

use App\Models\Traits\MeasuresQueueTime;
use Illuminate\Database\Eloquent\Model;

class SupJob extends Model
{
    use MeasuresQueueTime;

    protected $guarded = [];

    public function meta()
    {
        return $this->hasOne(\App\Models\Diagnostic\SupJobMeta::class);
    }

    public function inputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'input_stored_file_id');
    }

    public function outputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function getIsFinishedAttribute()
    {
        return $this->finished_at !== null;
    }

    public function getHasErrorAttribute()
    {
        return $this->error_message !== null;
    }
}
