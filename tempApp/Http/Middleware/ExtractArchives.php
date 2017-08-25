<?php

namespace App\Http\Middleware;

use App\Utils\Archive\Archive;
use Closure;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExtractArchives extends TransformsRequestFiles
{
    public function handle($request, Closure $next)
    {
        if($request->files->has('subtitles')) {
            $this->cleanFileBag($request->files);

            if(count($request->files->get('subtitles')) === 0) {
                return back()->withErrors(['subtitles' => __('validation.no_files_after_extracting_archives')]);
            }
        }

        return $next($request);
    }

    protected function cleanArray(array $data)
    {
        return collect($data)->map(function ($value, $key) {
            return collect([$this->cleanValue($key, $value)])->flatten()->all();
        })->all();
    }

    protected function transform($key, UploadedFile $file)
    {
        $archive = Archive::read($file->getRealPath());

        if($archive === null) {
            return $file;
        }

        $newUploadedFiles = [];

        foreach($archive->getFiles() as $compressedFile) {
            $filePath = $archive->extractFile($compressedFile);

            $newUploadedFile = new UploadedFile($filePath, $compressedFile->getName(), null, null, null, true);

            $newUploadedFile->_originalName = $compressedFile->getName();

            $newUploadedFiles[] = $newUploadedFile;

            register_shutdown_function(function() use ($filePath) {
                if(file_exists($filePath)) {
                    unlink($filePath);
                }
            });
        }

        return $newUploadedFiles;
    }
}
