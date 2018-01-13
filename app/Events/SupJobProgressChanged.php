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

    protected $statusMessage;

    public function __construct($supJob, $statusMessage)
    {
        $this->supJob = $supJob instanceof SupJob
            ? $supJob
            : SupJob::findOrFail($supJob);

        $this->statusMessage = $statusMessage;
    }

    public function broadcastOn()
    {
        return new Channel('sup-job.'.$this->supJob->url_key.'.progress');
    }

    public function broadcastWith()
    {
        return [
            'statusMessage' => $this->statusMessage,
        ];
    }
}
