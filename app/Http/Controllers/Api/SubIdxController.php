<?php

namespace App\Http\Controllers\Api;

use App\Models\SubIdx;

class SubIdxController
{
    public function languages($pageId)
    {
        return SubIdx::query()
            ->where('page_id', $pageId)
            ->firstOrFail()
            ->languages()
            ->get()
            ->map(function ($language) {
               return $language->getApiValues();
            });
    }
}
