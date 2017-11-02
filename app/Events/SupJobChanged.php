<?php

namespace App\Events;

use App\Http\Resources\SupJobResource;
use App\Models\SupJob;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SupJobChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcastQueue = 'broadcast';

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
        $request = request();

        return SupJobResource::make($this->supJob)->toArray($request);
    }
}
