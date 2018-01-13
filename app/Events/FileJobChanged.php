<?php

namespace App\Events;

use App\Models\FileJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileJobChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcastQueue = 'broadcast';

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
