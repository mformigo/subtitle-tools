<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileJob extends Model
{
    protected $guarded = [];

    public function inputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'input_stored_file_id');
    }

    public function outputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function getHasErrorAttribute()
    {
        return $this->error_message !== null;
    }

    public function getInputFilePathAttribute()
    {

    }
}
