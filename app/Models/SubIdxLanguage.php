<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubIdxLanguage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function subIdx()
    {
        return $this->belongsTo(SubIdx::class);
    }

    public function outputStoredFile()
    {
        return $this->hasOne(StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function getFileNameAttribute()
    {
        return $this->subIdx->original_name.'-'.$this->language.'.srt';
    }

    public function getIsQueuedAttribute()
    {
        return $this->queued_at !== null && $this->started_at === null;
    }

    public function getIsProcessingAttribute()
    {
        return $this->started_at !== null && $this->finished_at === null;
    }

    public function getQueuePositionAttribute()
    {
        if (! $this->is_queued) {
            return null;
        }

        return SubIdxLanguage::query()
            ->whereNotNull('queued_at')
            ->whereNull('started_at')
            ->where('queued_at', '<=', $this->queued_at)
            ->count();
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->output_stored_file_id === null) {
            return false;
        }

        return route('subIdx.download', [$this->subIdx->url_key, $this->index]);
    }
}
