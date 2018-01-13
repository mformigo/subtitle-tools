<?php

namespace App\Http\Controllers\Api;

use App\Models\SubIdx;
use App\Http\Controllers\Controller;

class SubIdxController extends Controller
{
    public function languages($pageId)
    {
        return SubIdx::where('page_id', $pageId)
            ->firstOrFail()
            ->languages()
            ->get()
            ->map(function ($language) {
               return $language->getApiValues();
            });
    }
}
