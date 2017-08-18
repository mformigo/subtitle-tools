<?php

namespace App\Http\Middleware;

use App\Utils\Archive\Archive;
use Closure;
use Illuminate\Http\UploadedFile;

class ExtractArchives
{
    public function handle($request, Closure $next, $fieldName = 'subtitles')
    {
        $uploadedFiles = array_wrap(request()->file($fieldName));
        $extractedArchives = [];
        $extractedTempFiles = [];

        foreach($uploadedFiles as $file) {
            if(!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }

            $archive = Archive::read($file->getRealPath());

            if($archive === null) {
                continue;
            }

            $compressedFiles = $archive->getFiles();

            $extractedArchives[] = $file;

            foreach($compressedFiles as $compressedFile) {
                $filePath = $archive->extractFile($compressedFile);
                $extractedTempFiles[] = $filePath;

                $newUploadedFile = new UploadedFile(
                    $filePath,
                    $compressedFile->getName(),
                    null, null, null,
                    true
                );

                $newUploadedFile->_originalName = $compressedFile->getName();

                $uploadedFiles[] = $newUploadedFile;
            }
        }

        if(count($extractedArchives) !== 0) {
            $uploadedFiles = array_values(
                array_diff($uploadedFiles, $extractedArchives)
            );

            if(count($uploadedFiles) === 0) {
                return back()->withErrors([$fieldName => __('validation.no_files_after_extracting_archives')]);
            }

            $request->files->add([$fieldName => $uploadedFiles]);

            register_shutdown_function(function() use ($extractedTempFiles) {
                foreach($extractedTempFiles as $filePath) {
                    if(file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            });
        }

        return $next($request);
    }
}
