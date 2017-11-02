<?php

namespace App\Http\Resources;

use App\Models\SupJob;
use Illuminate\Http\Resources\Json\Resource;

class SupJobResource extends Resource
{
    public function toArray($request)
    {
        /** @var SupJob $supJob */
        $supJob = $this->resource;

        return [
            'id'           => $supJob->id,
            'ocrLanguage'  => $supJob->ocr_language,
            'originalName' => $supJob->original_name,
            'isFinished'   => $supJob->is_finished,
            'errorMessage' => $supJob->has_error ? __($supJob->error_message) : false,
        ];
    }
}
