<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\UploadedFile;
use SjorsO\Archive\Archive;

class CheckFileSize
{
    public function handle($request, Closure $next)
    {
        $maxFileSizeBytes = 3 * 1024 * 1024;

        foreach($request->files->keys() as $key) {
            foreach(array_wrap($request->file($key)) as $file) {
                if($file instanceof UploadedFile && $file->isValid()) {

                    if(Archive::isReadable($file->getRealPath())) {
                        $compressedFiles = Archive::open($file->getRealPath())->getCompressedFiles();

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
