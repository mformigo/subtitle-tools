<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

abstract class TransformsRequestFiles
{
    public function handle($request, Closure $next)
    {
        $this->cleanFileBag($request->files);

        return $next($request);
    }

    protected function cleanFileBag(FileBag $bag)
    {
        $bag->replace($this->cleanArray($bag->all()));
    }

    protected function cleanArray(array $data)
    {
        return collect($data)->map(function ($value, $key) {
            return $this->cleanValue($key, $value);
        })->all();
    }

    protected function cleanValue($key, $value)
    {
        if (is_array($value)) {
            return $this->cleanArray($value);
        }

        if (!$value instanceof UploadedFile || !$value->isValid()) {
            return $value;
        }

        return $this->transform($key, $value);
    }

    protected abstract function transform($key, UploadedFile $file);
}
