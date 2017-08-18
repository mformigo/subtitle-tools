<?php

namespace App\Providers;

use App\Facades\FileHash;
use App\Utils\Archive\Archive;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CustomValidationRulesProvider extends ServiceProvider
{
    public function boot()
    {
        \Validator::extend('textfile', function ($attribute, $uploadedFile, $parameters, $validator) {
            if($uploadedFile instanceof UploadedFile) {
                return app('TextFileIdentifier')->isTextFile($uploadedFile->getRealPath());
            }

            return false;
        });

        \Validator::extend('file_not_empty', function ($attribute, $uploadedFile, $parameters, $validator) {
            if($uploadedFile instanceof UploadedFile && file_exists($uploadedFile->getRealPath())) {
                return filesize($uploadedFile->getRealPath()) > 0;
            }

            return false;
        });

        \Validator::extend('uploaded_files', function ($attribute, $valuesArray, $parameters, $validator) {
            $uploadedFiles = request()->files->get($attribute);

            foreach(array_wrap($uploadedFiles) as $file) {
                if(!$file instanceof UploadedFile) {
                    return false;
                }

                if($file->getError() !== UPLOAD_ERR_OK) {
                    return false;
                }

                if(!file_exists($file->getRealPath())) {
                    return false;
                }
            }

            return true;
        });

        \Validator::extend('no_archives_left', function ($attribute, $valuesArray, $parameters, $validator) {

            $uploadedFiles = request()->files->get($attribute);

            // Empty archives return an application/octet-stream mime, they have to matches by hash
            $emptyArchivesHashes = [
                'b04f3ee8f5e43fa3b162981b50bb72fe1acabb33',
            ];

            foreach(array_wrap($uploadedFiles) as $file) {
                if($file instanceof UploadedFile && $file->getError() === UPLOAD_ERR_OK) {
                    if(in_array(FileHash::make($file->getRealPath()), $emptyArchivesHashes)) {
                        return false;
                    }

                    if(Archive::read($file->getRealPath()) !== null) {
                        return false;
                    }
                }
            }

            return true;
        });
    }

    public function register()
    {

    }

}
