<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FileGroup
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $original_name
 * @property string $tool_route
 * @property string $url_key
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FileJob[] $fileJobs
 * @property-read mixed $result_route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileGroup whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileGroup whereToolRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileGroup whereUrlKey($value)
 * @mixin \Eloquent
 */
class FileGroup extends Model
{
    protected $guarded = [];

    public function fileJobs()
    {
        return $this->hasMany(\App\Models\FileJob::class);
    }

    public function archiveStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'archive_stored_file_id');
    }

    public function getResultRouteAttribute()
    {
        return route("{$this->tool_route}-result", ['urlKey' => $this->url_key]);
    }

    public function setJobOptionsAttribute(array $options)
    {
        $this->attributes['job_options'] = count($options) === 0 ? '{}' : json_encode($options);
    }

    public function getJobOptionsAttribute()
    {
        return json_decode($this->attributes['job_options']);
    }

    public function getArchiveStatusAttribute()
    {
        if($this->file_jobs_finished_at === null) {
            return __('messages.archive.not_available_yet');
        }
        else if($this->archive_requested_at === null) {
            return __('messages.archive.request');
        }
        else if($this->archive_stored_file_id === null) {
            return __('messages.archive.failed');
        }

        return __('messages.archive.download');
    }

    public function getRequestArchiveUrlAttribute()
    {
        if($this->file_jobs_finished_at === null || $this->archive_requested_at !== null) {
            return false;
        }

        return route('file-group-request-archive', ['urlKey' => $this->url_key]);
    }

    public function getDownloadArchiveUrlAttribute()
    {
        if($this->archive_stored_file_id === null) {
            return false;
        }

        return route('file-group-archive-download', ['urlKey' => $this->url_key]);
    }

    public function getApiValues()
    {
        return [
            'archiveStatus' => $this->archiveStatus,
            'requestArchiveUrl' => $this->requestArchiveUrl,
            'downloadArchiveUrl' => $this->downloadArchiveUrl,
        ];
    }
}
