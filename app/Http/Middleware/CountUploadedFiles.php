<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\Archive\Archive;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CountUploadedFiles
{
    public function handle($request, Closure $next)
    {
        $fileCount = 0;
        $fileLimit = 100;

        foreach ($request->files->keys() as $key) {
            foreach (array_wrap($request->files->get($key)) as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $fileCount++;

                    if (Archive::isReadable($file->getRealPath())) {
                        $fileCount += Archive::open($file->getRealPath())->getEntriesCount() - 1;
                    }

                    if ($fileCount > $fileLimit) {
                        return back()->withErrors(['subtitles' => __('validation.too_many_files_including_archives')]);
                    }
                }
            }
        }

        return $next($request);
    }
}
