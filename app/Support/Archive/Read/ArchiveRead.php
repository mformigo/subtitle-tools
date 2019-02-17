<?php

namespace App\Support\Archive\Read;

use RunTimeException;
use App\Support\Archive\CompressedFile;

abstract class ArchiveRead
{
    protected $successfullyOpened = false;

    protected static $archiveMimeType = null;

    abstract public function __construct($filePath);

    /**
     * @return CompressedFile[]
     */
    abstract public function getCompressedFiles();

    /**
     * Return the amount of entries in this archive. Directories count as an entry.
     *
     * @return int
     */
    abstract public function getEntriesCount();

    public function isSuccessfullyOpened()
    {
        return $this->successfullyOpened;
    }

    /**
     * Extract a file to the destination directory with a random file name.
     *
     * @param CompressedFile $file
     * @param $destinationDirectory
     *
     * @return string File path to the extracted file
     *
     * @throws RunTimeException
     */
    public function extractFile(CompressedFile $file, $destinationDirectory)
    {
        if (! file_exists($destinationDirectory)) {
            throw new RunTimeException('Path does not exist: '.$destinationDirectory);
        }

        if (! is_dir($destinationDirectory)) {
            throw new RunTimeException('Path is not a directory: '.$destinationDirectory);
        }

        $destinationDirectory = rtrim($destinationDirectory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        $outputFileName = date('Y-z').'-'.bin2hex(random_bytes(10));

        $this->extract($file, $destinationDirectory, $outputFileName);

        return $destinationDirectory.$outputFileName;
    }

    abstract protected function extract(CompressedFile $file, $destinationDirectory, $outputFileName);

    public static function isThisFormat($filePath)
    {
        if (! file_exists($filePath)) {
            throw new RunTimeException('File does not exist: '.$filePath);
        }

        $mimeType = file_mime($filePath);

        if ($mimeType === static::$archiveMimeType) {
            $archiveRead = new static($filePath);

            return $archiveRead !== null && $archiveRead->isSuccessfullyOpened();
        }

        if (static::additionalFormatCheck($filePath)) {
            return true;
        }

        return false;
    }

    abstract public static function isAvailable();

    protected static function additionalFormatCheck($filePath)
    {
        return false;
    }
}
