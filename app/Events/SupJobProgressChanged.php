<?php

namespace App\Events;

use App\Models\SupJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SupJobProgressChanged extends BaseEvent implements ShouldBroadcast
{
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
