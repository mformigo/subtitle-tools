<?php

namespace App\Models;

use App\Support\Facades\FileName;
use Illuminate\Database\Eloquent\Model;

class FileJob extends Model
{
    protected $guarded = [];

    public function fileGroup()
    {
        return $this->belongsTo(FileGroup::class);
    }

    public function inputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'input_stored_file_id');
    }

    public function outputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function jobOptions()
    {
        return $this->fileGroup()->job_options;
    }

    public function getHasErrorAttribute()
    {
        return $this->error_message !== null;
    }

    public function getHasFinishedAttribute()
    {
        return $this->finished_at !== null;
    }

    public function getOriginalNameWithNewExtensionAttribute()
    {
        // new_extension is only set after the job has successfully finished
        if (empty($this->new_extension)) {
            return $this->original_name;
        }

        return FileName::changeExtension($this->original_name, $this->new_extension);
    }

    public function getApiValues()
    {
        return [
            'id'           => $this->id,
            'originalName' => $this->originalNameWithNewExtension,
            'isFinished'   => $this->has_finished,
            'errorMessage' => $this->has_error ? __($this->error_message) : false,
        ];
    }
}
