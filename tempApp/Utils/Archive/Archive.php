<?php

namespace App\Utils\Archive;

use App\Utils\Archive\Read\ArchiveReadInterface;
use App\Utils\Archive\Read\ZipArchiveRead;

class Archive
{
    protected static $archiveReadClasses = [
        ZipArchiveRead::class,
    ];

    private function __construct()
    {
    }

    /**
     * @param $filePath
     * @return null|ArchiveReadInterface
     */
    public static function read($filePath)
    {
        foreach(static::$archiveReadClasses as $archiveClass) {
            if($archiveClass::isThisFormat($filePath)) {
                return new $archiveClass($filePath);
            }
        }

        return null;
    }

    public static function isArchive($filePath, $strict = true)
    {
        foreach(static::$archiveReadClasses as $archiveClass) {
            if($archiveClass::isThisFormat($filePath, $strict)) {
                return true;
            }
        }

        return false;
    }
}
