<?php

namespace App\Providers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\ServiceProvider;

class CustomValidationRulesProvider extends ServiceProvider
{
    public function boot()
    {
        \Validator::extend('textfile', function ($attribute, $uploadedFile, $parameters, $validator) {
            if(!($uploadedFile instanceof UploadedFile)) {
                return false;
            }

            $textFileIdentifier = app(\App\Utils\TextFileIdentifier::class);

            return $textFileIdentifier->isTextFile($uploadedFile->getRealPath());
        });
    }

    public function register()
    {

    }

}
