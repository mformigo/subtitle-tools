<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use App\Support\TextFile\Exceptions\TextEncodingException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        PostTooLargeException::class,
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof PostTooLargeException) {
            // Somehow, using back()->withErrors doesn't work on the live server,
            // so this hack is used instead
            return response()->json([
                __('validation.file_larger_than_max_post_size'),
            ])->setStatusCode(500);
        }

        if ($exception instanceof TextEncodingException) {
            return response()->view('errors.text-encoding-exception', [], 500);
        }

        return parent::render($request, $exception);
    }
}
