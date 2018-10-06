<?php

namespace App\Support\Archive\Read;

use App\Support\Archive\CompressedFile;
use ZipArchive;

class ZipArchiveRead extends ArchiveRead
{
    protected static $archiveMimeType = 'application/zip';

    protected $zip;

    public function __construct($filePath)
    {
        $this->zip = new ZipArchive();

        $this->successfullyOpened = ($this->zip->open($filePath) === true);
    }

    /**
     * Return the amount of entries in this archive. Directories count as an entry.
     *
     * @return int
     */
    public function getEntriesCount()
    {
        return $this->zip->numFiles;
    }

    /**
     * @return CompressedFile[]
     */
    public function getCompressedFiles()
    {
        $compressedFiles = [];

        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $stat = $this->zip->statIndex($i);

            // Skip directories
            if (strlen($stat['name']) === 0 || substr($stat['name'], -1) === DIRECTORY_SEPARATOR) {
                continue;
            }

            $compressedFiles[] = new CompressedFile($i, $stat['name'], $stat['size']);
        }

        return $compressedFiles;
    }

    protected function extract(CompressedFile $file, $destinationDirectory, $outputFileName)
    {
        $this->zip->renameName($file->getName(), $outputFileName);

        $this->zip->extractTo($destinationDirectory, [$outputFileName]);

        $this->zip->unchangeName($outputFileName);
    }

    protected static function additionalFormatCheck($filePath)
    {
        // empty zip files have a application/octet-stream mime, compare them with the hash of an empty zip
        if (sha1_file($filePath) === 'b04f3ee8f5e43fa3b162981b50bb72fe1acabb33') {
            return true;
        }

        return false;
    }

    public static function isAvailable()
    {
        return class_exists('\ZipArchive');
    }
}
