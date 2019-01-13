<?php

namespace App\Events;

use App\Models\FileJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileJobChanged extends BaseEvent implements ShouldBroadcast
{
    protected $fileJob;

    public function __construct(FileJob $fileJob)
    {
        $this->fileJob = $fileJob;
    }

    public function broadcastOn()
    {
        return new Channel('file-group.'.$this->fileJob->fileGroup->url_key.'.jobs');
    }

    public function broadcastWith()
    {
        return $this->fileJob->getApiValues();
    }
}
