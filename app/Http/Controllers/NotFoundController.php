<?php

namespace App\Http\Controllers;

use SjorsO\TextFile\Facades\TextFileReader;
use Illuminate\Http\Request;

class NotFoundController extends Controller
{
    public function index(Request $request)
    {
        $requestedPath = $request->getPathInfo();

        if (! $this->isBlacklisted($requestedPath)) {
            $timestamp = now();

            $userIp = $request->ip();

            file_put_contents(
                storage_disk_file_path('diagnostic/404.txt'),
                "{$timestamp}|{$userIp}|{$requestedPath}\r\n",
                FILE_APPEND
            );
        }

        return response()->view('errors.404')->setStatusCode(404);
    }

    protected function isBlacklisted($requestedPath)
    {
        $blacklistFilePath = storage_disk_file_path('diagnostic/404-blacklist.txt');

        if (! file_exists($blacklistFilePath)) {
            touch($blacklistFilePath);
        }

        $blackList = TextFileReader::getLines($blacklistFilePath);

        return in_array($requestedPath, $blackList);
    }
}
