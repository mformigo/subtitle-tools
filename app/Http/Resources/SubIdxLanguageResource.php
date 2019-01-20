<?php

namespace App\Http\Resources;

use App\Models\SubIdxLanguage;
use Illuminate\Http\Resources\Json\Resource;

class SubIdxLanguageResource extends Resource
{
    public function toArray($request = null)
    {
        /** @var SubIdxLanguage $language */
        $language = $this->resource;

        return [
            'id' => $language->id,
            'index' => $language->index,
            'language' => __("languages.subIdx.$language->language"),
            'hasError' => $language->error_message !== null,
            'canBeRequested' => $language->queued_at === null,
            'isQueued' => $language->is_queued,
            'queuePosition' => $language->queue_position,
            'isProcessing' => $language->is_processing,
            'downloadUrl' => $language->download_url,
        ];
    }
}
