<?php

function generate_url_key()
{
    return str_random(16);
}

function file_mime($filePath)
{
    if (!file_exists($filePath)) {
        throw new \Exception("File does not exist ({$filePath})");
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

    return rtrim($storagePath, '/') . "/" . ltrim($path, '/');
}

function nav_item($routeName)
{
    $active = Request::routeIs($routeName) || Request::routeIs("{$routeName}.*") ? ' class="active"' : '';

    return "<li{$active}><a href='" . route($routeName) . "'>" . __("nav.item.{$routeName}") . "</a></li>";
}

function interval(int $interval, $closure)
{
    $interval = ($interval === 0) ? 1 : $interval;

    static $calls = [];

    $caller = sha1(debug_backtrace()[0]['file'] . '|' . debug_backtrace()[0]['line']);

    $callCount = $calls[$caller] ?? 1;

    if ($callCount % $interval === 0) {
        $closure();
    }

    $calls[$caller] = $callCount + 1;
}

function once($closure)
{
    static $calls = [];

    $caller = sha1(debug_backtrace()[0]['file'] . '|' . debug_backtrace()[0]['line']);

    if (isset($calls[$caller])) {
        return;
    }

    $calls[$caller] = true;

    $closure();
}
