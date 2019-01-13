<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubIdxLanguage extends Model
{
    protected $guarded = [];

    public function subIdx()
    {
        return $this->belongsTo(SubIdx::class);
    }

    public function outputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function getFilePathAttribute()
    {
        return $this->outputStoredFile()->firstOrFail()->filePath;
    }

    public function getFileNameAttribute()
    {
        return $this->subIdx->original_name.'-'.$this->language.'.srt';
    }

    public function getHasStartedAttribute()
    {
        return $this->started_at !== null;
    }

    public function getHasFinishedAttribute()
    {
        return $this->finished_at !== null;
    }

    public function getHasErrorAttribute()
    {
        return $this->error_message !== null;
    }

    public function getStatusMessageAttribute()
    {
        if (! $this->hasStarted) {
            return __('messages.status.queued');
        }

        if (! $this->hasFinished) {
            return __('messages.status.processing');
        }

        if ($this->hasError) {
            return __('messages.status.failed');
        }

        return __('messages.status.finished');
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->output_stored_file_id === null) {
            return false;
        }

        return route('subIdx.download', [
            'urlKey' => $this->subIdx->page_id,
            'index'  => $this->index,
        ]);
    }

    public function getApiValues()
    {
        return [
            'index' => $this->index,
            'countryCode' => $this->language,
            'language' => __('languages.'.$this->language),
            'status' => $this->statusMessage,
            'hasError' => $this->hasError,
            'downloadUrl' => $this->downloadUrl,
            'isFinished' => $this->has_finished,
        ];
    }
}
