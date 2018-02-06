<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileGroup extends Model
{
    protected $guarded = [];

    public function fileJobs()
    {
        return $this->hasMany(FileJob::class);
    }

    public function archiveStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'archive_stored_file_id');
    }

    public function getResultRouteAttribute()
    {
        return route("{$this->tool_route}.result", ['urlKey' => $this->url_key]);
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
        switch (null) {
            case $this->file_jobs_finished_at:  return __('messages.archive.not_available_yet');
            case $this->archive_requested_at:   return __('messages.archive.request');
            case $this->archive_finished_at:    return __('messages.archive.processing');
            case $this->archive_stored_file_id: return __('messages.archive.failed');
            default:                            return __('messages.archive.download');
        }
    }

    public function getArchiveRequestUrlAttribute()
    {
        if ($this->file_jobs_finished_at === null || $this->archive_requested_at !== null) {
            return false;
        }

        return route('fileGroupRequestArchive', ['urlKey' => $this->url_key]);
    }

    public function getArchiveDownloadUrlAttribute()
    {
        if ($this->archive_stored_file_id === null) {
            return false;
        }

        return route('fileGroupArchiveDownload', ['urlKey' => $this->url_key]);
    }

    public function getApiValues()
    {
        return [
            'archiveStatus'      => $this->archiveStatus,
            'archiveRequestUrl'  => $this->archiveRequestUrl,
            'archiveDownloadUrl' => $this->archiveDownloadUrl,
        ];
    }

    public static function findForTool($urlKey, $indexRouteName)
    {
        return static::query()
            ->where('url_key', $urlKey)
            ->where('tool_route', $indexRouteName)
            ->firstOrFail();
    }
}
