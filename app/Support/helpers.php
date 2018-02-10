<?php

use App\Models\StoredFile;

/**
 * @param $file string|StoredFile
 *
 * @return bool
 */
function is_text_file($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    }

    return TextFileIdentifier::isTextFile($file);
}

function generate_url_key()
{
    return strtolower(str_random(16));
}

function file_mime($filePath)
{
    if (! file_exists($filePath)) {
        throw new RuntimeException('File does not exist: '.$filePath);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    return $mimeType;
}

function storage_disk_file_path($path, $disk = null)
{
    $disk = $disk ?: env('FILESYSTEM_DRIVER');

    $storagePath = Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix();

    return rtrim($storagePath, '/').'/'.ltrim($path, '/');
}

function interval(int $interval, $closure)
{
    $interval = ($interval === 0) ? 1 : $interval;

    static $calls = [];

    $caller = sha1(debug_backtrace()[0]['file'].'|'.debug_backtrace()[0]['line']);

    $callCount = $calls[$caller] ?? 1;

    if ($callCount % $interval === 0) {
        $closure();
    }

    $calls[$caller] = $callCount + 1;
}

function once($closure)
{
    static $calls = [];

    $caller = sha1(debug_backtrace()[0]['file'].'|'.debug_backtrace()[0]['line']);

    if (isset($calls[$caller])) {
        return;
    }

    $calls[$caller] = true;

    $closure();
}
