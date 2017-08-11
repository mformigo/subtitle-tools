<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubIdxLanguage
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $sub_idx_id
 * @property string $index
 * @property string $language
 * @property string|null $filename
 * @property string|null $finished_at
 * @property string $filePath
 * @property-read \App\Models\SubIdx $subIdx
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereSubIdxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $has_error
 * @property string|null $started_at
 * @property-read mixed $file_path
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereHasError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereStartedAt($value)
 * @property-read mixed $download_url
 * @property-read mixed $has_finished
 * @property-read mixed $has_started
 * @property-read mixed $status_message
 */
class SubIdxLanguage extends Model
{
    protected $fillable = ['index', 'language', 'filename', 'has_error', 'started_at', 'finished_at'];

    public function subIdx()
    {
        return $this->belongsTo('App\Models\SubIdx');
    }

    public function getFilePathAttribute()
    {
        return storage_disk_file_path($this->subIdx->store_directory . $this->filename);
    }

    public function getHasStartedAttribute()
    {
        return $this->started_at !== null;
    }

    public function getHasFinishedAttribute()
    {
        return $this->finished_at !== null;
    }

    public function getStatusMessageAttribute()
    {
        switch(false)
        {
            case $this->hasStarted:  return __('messages.status.queued');
            case $this->hasFinished: return __('messages.status.processing');
            case !$this->has_error:  return __('messages.status.failed');
            default:                 return __('messages.status.finished');
        }
    }

    public function getDownloadUrlAttribute()
    {
        if($this->statusMessage !== __('messages.status.finished')) {
            return false;
        }

        return route('sub-idx-dl', [
            'pageId' => $this->subIdx->page_id,
            'index'  => $this->index,
        ]);
    }

    public function getApiValues()
    {
        return [
            'index'       => $this->index,
            'countryCode' => $this->language,
            'language'    => __("languages.{$this->language}"),
            'status'      => $this->statusMessage,
            'downloadUrl' => $this->downloadUrl,
        ];
    }
}
