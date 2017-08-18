<?php

namespace App\Utils\Archive;

use App\Utils\Archive\Read\ZipArchiveRead;

class Archive
{
    private function __construct()
    {
    }

    public static function read($filePath)
    {
        $mime = file_mime($filePath);

        $archiveRead = null;

        switch($mime)
        {
            case 'application/zip':
                $archiveRead = new ZipArchiveRead($filePath);
                break;
        }

        if($archiveRead === null || !$archiveRead->isSuccessfullyOpened()) {
            return null;
        }

        return $archiveRead;
    }
}