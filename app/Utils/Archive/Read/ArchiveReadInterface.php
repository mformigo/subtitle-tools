<?php

namespace App\Utils\Archive\Read;

use App\Utils\Archive\CompressedFile;

interface ArchiveReadInterface
{
    public function __construct($filePath);

    public function isSuccessfullyOpened();

    public function getFileCount();

    /**
     * @return CompressedFile[]
     */
    public function getFiles();

    /**
     * Extracts a file to the destination directory with a random file name
     * @param CompressedFile $file
     * @param $destinationDirectory
     * @return string File path of the extracted file
     */
    public function extractFile(CompressedFile $file, $destinationDirectory);
}