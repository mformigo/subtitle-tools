<?php

namespace App\Models;

use App\Models\Traits\MeasuresQueueTime;
use Illuminate\Database\Eloquent\Model;

class SupJob extends Model
{
    use MeasuresQueueTime;

    protected $guarded = [];

    public function supGroup()
    {
        return $this->belongsTo(\App\Models\SupGroup::class);
    }

    public function inputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'input_stored_file_id');
    }

    public function outputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'output_stored_file_id');
    }
}
