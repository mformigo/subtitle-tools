<?php

namespace App\Support\Archive;

use App\Support\Archive\Read\ArchiveRead;
use App\Support\Archive\Read\RarArchiveRead;
use App\Support\Archive\Read\ZipArchiveRead;

class Archive
{
    protected static $archiveReadClasses = [
        ZipArchiveRead::class,
        RarArchiveRead::class,
    ];

    protected static $isInitialized = false;

    private function __construct()
    {
    }

    /**
     * @param $filePath
     *
     * @return ArchiveRead|null
     */
    public static function open($filePath)
    {
        $archiveClass = static::getFormat($filePath);

        return ($archiveClass === null) ? null : new $archiveClass($filePath);
    }

    /**
     * @param $filePath
     *
     * @return bool
     */
    public static function isReadable($filePath)
    {
        return static::getFormat($filePath) !== null;
    }

    /**
     * If this file is readable by an available ArchiveRead implementation,
     * return the fully qualified class name of this implementation.
     *
     * @param $filePath
     *
     * @return string|null
     */
    protected static function getFormat($filePath)
    {
        if (! static::$isInitialized) {
            static::filterAvailableFormats();

            static::$isInitialized = true;
        }

        foreach (static::$archiveReadClasses as $archiveClass) {
            if ($archiveClass::isThisFormat($filePath)) {
                return $archiveClass;
            }
        }

        return null;
    }

    /**
     * Every ArchiveRead implementation provides an "isAvailable" method. Here
     * we filter out every archive format that is not available. For example,
     * if you don't have the RAR PECL package installed, the RarArchiveRead
     * class is not available.
     */
    protected static function filterAvailableFormats()
    {
        static::$archiveReadClasses = array_filter(static::$archiveReadClasses, function ($archiveClass) {
            return $archiveClass::isAvailable();
        });
    }
}
