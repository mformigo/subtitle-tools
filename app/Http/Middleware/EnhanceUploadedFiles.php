<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EnhanceUploadedFiles
{
    public function handle($request, Closure $next)
    {
        // we use the _originalName property when extracting zips,
        // to keep everything simple, we add it here to single files too

        foreach($request->files->keys() as $key) {
            foreach(array_wrap($request->file($key)) as $file) {
                if($file instanceof UploadedFile) {
                    $file->_originalName = $file->getClientOriginalName();
                }
            }
        }

        return $next($request);
    }
}
