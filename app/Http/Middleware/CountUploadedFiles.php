<?php

namespace App\Http\Middleware;

use App\Utils\Archive\Archive;
use Closure;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CountUploadedFiles
{
    public function handle($request, Closure $next)
    {
        $fileCount = 0;
        $fileLimit = 100;

        foreach($request->files->keys() as $key) {
            foreach(array_wrap($request->file($key)) as $file) {
                if($file instanceof UploadedFile && $file->isValid()) {
                    $fileCount++;

                    if(Archive::isArchive($file->getRealPath())) {
                        $fileCount += Archive::read($file->getRealPath())->getEntriesCount() - 1;
                    }

                    if($fileCount > $fileLimit) {
                        return back()->withErrors(['subtitles' => __('validation.too_many_files_including_archives')]);
                    }
                }
            }
        }

        return $next($request);
    }
}
