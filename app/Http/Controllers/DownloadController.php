<?php

namespace App\Http\Controllers;

use App\Models\TextFileJob;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function index($urlKey)
    {
        $textFileJob = TextFileJob::where('url_key', $urlKey)->firstOrFail();

        return view('download-index', [
            'originalName' => $textFileJob->original_file_name,
            'returnUrl' => route($textFileJob->tool_route),
        ]);
    }
}
