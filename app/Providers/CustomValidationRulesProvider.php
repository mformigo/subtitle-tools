<?php

namespace App\Providers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\ServiceProvider;

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
    }

    public function register()
    {

    }

}
