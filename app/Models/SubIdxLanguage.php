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
 * @property int|null $output_stored_file_id
 * @property string|null $error_message
 * @property-read mixed $file_name
 * @property-read \App\Models\StoredFile $outputStoredFile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereOutputStoredFileId($value)
 * @property int|null $queue_time
 * @property int|null $extract_time
 * @property int|null $timed_out
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereExtractTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereQueueTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereTimedOut($value)
 */
class SubIdxLanguage extends Model
{
    protected $guarded = [];

    public function subIdx()
    {
        return $this->belongsTo(\App\Models\SubIdx::class);
    }

    public function outputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function vobsubOutput()
    {
        return Vobsub2srtOutput::query()
            ->where('sub_idx_id', $this->sub_idx_id)
            ->where('index', $this->index)
            ->first();
    }

    public function getFilePathAttribute()
    {
        return $this->outputStoredFile()->firstOrFail()->filePath;
    }

    public function getFileNameAttribute()
    {
        return $this->subIdx->original_name . '-' . $this->language . '.srt';
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
        if(!$this->hasStarted) {
            return __('messages.status.queued');
        }

        if(!$this->hasFinished) {
            return __('messages.status.processing');
        }

        if($this->hasError) {
            return __('messages.status.failed');
        }

        return __('messages.status.finished');
    }

    public function getDownloadUrlAttribute()
    {
        if($this->output_stored_file_id === null) {
            return false;
        }

        return route('subIdxDownload', [
            'urlKey' => $this->subIdx->page_id,
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
            'hasError'    => $this->hasError,
            'downloadUrl' => $this->downloadUrl,
        ];
    }
}
