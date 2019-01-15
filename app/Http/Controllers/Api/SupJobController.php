<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SupJobResource;
use App\Models\SupJob;

class SupJobController
{
    public function show($urlKey)
    {
        $supJob = SupJob::where('url_key', $urlKey)->firstOrFail();

        return new SupJobResource($supJob);
    }
}
