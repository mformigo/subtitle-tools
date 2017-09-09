<?php

namespace App\Utils\Archive\Read;

use App\Utils\Archive\CompressedFile;
use RarArchive;

class RarArchiveRead implements ArchiveReadInterface
{
    protected $isSuccessfullyOpened = false;

    protected $rar;

    public function __construct($filePath)
    {
        $this->rar = @RarArchive::open($filePath);

        $opened = ($this->rar !== false);

        if($opened) {
            $this->isSuccessfullyOpened = (@$this->rar->isBroken() === false);
        }
    }

    public function isSuccessfullyOpened()
    {
        return $this->isSuccessfullyOpened;
    }

    /**
     * @return int Total number of archive entries (including directories)
     */
    public function getEntriesCount()
    {
        return count($this->rar->getEntries());
    }

    /**
     * @return CompressedFile[]
     */
    public function getFiles()
    {
        $compressedFiles = [];

        $entries = $this->rar->getEntries();

        for($i = 0; $i < count($entries); $i++) {
            if($entries[$i]->isDirectory() || $entries[$i]->isEncrypted()) {
                continue;
            }

            $newFile = (new CompressedFile())
                ->setIndex($i)
                ->setName($entries[$i]->getName())
                ->setRealSize($entries[$i]->getUnpackedSize());

            $compressedFiles[] = $newFile;
        }

        return $compressedFiles;
    }

    /**
     * Extracts a file to the destination directory with a random file name
     * @param CompressedFile $file
     * @param $destinationDirectory
     * @return string File path of the extracted file
     */
    public function extractFile(CompressedFile $file, $destinationDirectory = null)
    {
        if($destinationDirectory === null) {
            $destinationDirectory = storage_disk_file_path('temporary-files/');
        }

        $destinationDirectory = rtrim($destinationDirectory, DIRECTORY_SEPARATOR);

        if(!file_exists($destinationDirectory) || !is_dir($destinationDirectory)) {
            throw new \InvalidArgumentException();
        }

        $newName = date('Y-z') . '-archive-' . str_random(16);

        $destinationFilePath = $destinationDirectory . DIRECTORY_SEPARATOR . $newName;

        $rarEntry = $this->rar->getEntries()[$file->getIndex()];

        $rarEntry->extract(false, $destinationFilePath);

        return $destinationFilePath;
    }

    public static function isThisFormat($filePath, $strict = true)
    {
        if(!$strict) {
            return ends_with(strtolower($filePath), '.rar');
        }

        if(file_mime($filePath) === 'application/x-rar') {
            $archiveRead = new RarArchiveRead($filePath);

            return $archiveRead !== null && $archiveRead->isSuccessfullyOpened();
        }

        return false;
    }
}
