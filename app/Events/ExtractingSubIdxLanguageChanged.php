<?php

namespace App\Events;

use App\Models\SubIdxLanguage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExtractingSubIdxLanguageChanged extends BaseEvent implements ShouldBroadcast
{
    protected $subIdxLanguage;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->subIdxLanguage = $subIdxLanguage;
    }

    public function broadcastOn()
    {
        return new Channel('sub-idx.'.$this->subIdxLanguage->subIdx->page_id);
    }

    public function broadcastWith()
    {
        return $this->subIdxLanguage->getApiValues();
    }
}
