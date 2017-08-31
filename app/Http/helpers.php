<?php

function file_mime($filePath)
{
    if(!file_exists($filePath)) {
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
    $active = Request::routeIs($routeName . '*') ? ' class="active"' : '';

    return "<li{$active}><a href='" . route($routeName) . "'>" . __("nav.item.{$routeName}") . "</a></li>";
}
