<?php

use App\Models\StoredFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @param $file string|StoredFile|UploadedFile
 *
 * @return array
 */
function read_lines($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    } elseif ($file instanceof UploadedFile) {
        $file = $file->getRealPath();
    }

    return TextFileReader::getLines($file);
}

/**
 * @param $file string|StoredFile|UploadedFile
 *
 * @return string
 */
function read_content($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    } elseif ($file instanceof UploadedFile) {
        $file = $file->getRealPath();
    }

    return TextFileReader::getContent($file);
}

/**
 * @param $file string|StoredFile|UploadedFile
 *
 * @return bool
 */
function is_text_file($file)
{
    if ($file instanceof StoredFile) {
        $file = $file->file_path;
    } elseif ($file instanceof UploadedFile) {
        $file = $file->getRealPath();
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

function format_file_size($bytes)
{
    $units = ['b', 'kb', 'mb', 'gb', 'tb'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, 0).' '.$units[$pow];
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
