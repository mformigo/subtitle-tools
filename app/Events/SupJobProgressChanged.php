<?php

namespace App\Events;

use App\Models\SupJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SupJobProgressChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcastQueue = 'broadcast';

    protected $supJob;

    protected $progress;

    public function __construct(SupJob $supJob, int $progress)
    {
        $this->supJob = $supJob;

        $this->progress = $progress;
    }

    public function broadcastOn()
    {
        return new Channel('sup-job.'.$this->supJob->url_key.'.progress');
    }

    public function broadcastWith()
    {
        return [
            'progress' => $this->progress,
        ];
    }
}
