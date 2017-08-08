<?php

namespace App\Events;

use App\Models\SubIdxLanguage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExtractingSubIdxLanguageChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $subIdxLanguage;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->subIdxLanguage = $subIdxLanguage;
    }

    public function broadcastOn()
    {
        return new Channel("sub-idx.{$this->subIdxLanguage->subIdx->page_id}");
    }

    public function broadcastWith()
    {
        return $this->subIdxLanguage->getApiValues();
    }
}
