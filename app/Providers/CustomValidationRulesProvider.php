<?php

namespace App\Providers;

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

                if(!$file->isValid()) {
                    return false;
                }

                if(!file_exists($file->getRealPath())) {
                    return false;
                }
            }

            return true;
        });
    }

    public function register()
    {

    }

}
