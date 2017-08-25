<?php

namespace App\Http\Middleware;

use App\Utils\Archive\Archive;
use Closure;
use Illuminate\Http\UploadedFile;

class CheckFileSize
{
    public function handle($request, Closure $next)
    {
        $maxFileSizeBytes = 3 * 1024 * 1024;

        foreach($request->files->keys() as $key) {
            foreach(array_wrap($request->file($key)) as $file) {
                if($file instanceof UploadedFile && $file->isValid()) {

                    if(Archive::isArchive($file->getRealPath())) {
                        $compressedFiles = Archive::read($file->getRealPath())->getFiles();

                        foreach($compressedFiles as $compressedFile) {
                            if($compressedFile->getRealSize() > $maxFileSizeBytes) {
                                return back()->withErrors(['subtitles' => __('validation.a_file_in_archive_too_big_when_extracted')]);
                            }
                        }
                    }
                    else {
                        if($file->getSize() > $maxFileSizeBytes) {
                            return back()->withErrors(['subtitles' => __('validation.a_file_is_too_big')]);
                        }
                    }

                }
            }
        }

        return $next($request);
    }
}
