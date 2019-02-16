<?php

namespace App\Models;

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
}
