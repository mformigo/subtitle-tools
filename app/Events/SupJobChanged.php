<?php

namespace App\Events;

use App\Http\Resources\SupJobResource;
use App\Models\SupJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SupJobChanged extends BaseEvent implements ShouldBroadcast
{
    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function broadcastOn()
    {
        return new Channel('sup-job.'.$this->supJob->url_key);
    }

    public function broadcastWith()
    {
        return SupJobResource::make($this->supJob)->toArray();
    }
}
