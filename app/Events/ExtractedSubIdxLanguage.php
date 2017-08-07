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

class ExtractedSubIdxLanguage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $pageId;
    private $languageIndex;
    private $languageHasError;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->pageId = $subIdxLanguage->subIdx->page_id;

        $this->languageIndex = $subIdxLanguage->index;

        $this->languageHasError = $subIdxLanguage->has_error;
    }

    public function broadcastOn()
    {
        return new Channel("sub-idx.{$this->pageId}.{$this->languageIndex}");
    }

    public function broadcastWith()
    {
        return [
          'index' => $this->languageIndex,
          'hasError' => $this->languageHasError,
        ];
    }

}
