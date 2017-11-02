<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SupJobResource;
use App\Http\Controllers\Controller;
use App\Models\SupJob;

class SupJobController extends Controller
{
    public function show($urlKey)
    {
        $supJob = SupJob::where('url_key', $urlKey)->firstOrFail();

        return new SupJobResource($supJob);
    }
}
