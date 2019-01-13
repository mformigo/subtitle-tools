<?php

namespace App\Events;

use App\Models\FileGroup;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileGroupChanged extends BaseEvent implements ShouldBroadcast
{
    protected $fileGroup;

    public function __construct(FileGroup $fileGroup)
    {
        $this->fileGroup = $fileGroup;
    }

    public function broadcastOn()
    {
        return new Channel('file-group.'.$this->fileGroup->url_key);
    }

    public function broadcastWith()
    {
        return $this->fileGroup->getApiValues();
    }
}
