<?php

namespace App\Events;

use App\Models\FileGroup;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileGroupChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcastQueue = 'broadcast';

    protected $fileGroup;

    public function __construct(FileGroup $fileGroup)
    {
        $this->fileGroup = $fileGroup;
    }

    public function broadcastOn()
    {
        return new Channel("file-group.{$this->fileGroup->url_key}");
    }

    public function broadcastWith()
    {
        return $this->fileGroup->getApiValues();
    }
}
