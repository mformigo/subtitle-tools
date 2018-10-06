<?php

namespace App\Support\Archive\Read;

use RarArchive;
use App\Support\Archive\CompressedFile;

class RarArchiveRead extends ArchiveRead
{
    protected static $archiveMimeType = 'application/x-rar';

    protected $rar;

    public function __construct($filePath)
    {
        $this->rar = @RarArchive::open($filePath);

        if ($this->rar !== false) {
            $this->successfullyOpened = (@$this->rar->isBroken() === false);
        }
    }

    /**
     * Return the amount of entries in this archive. Directories count as an entry.
     *
     * @return int
     */
    public function getEntriesCount()
    {
        return count($this->rar->getEntries());
    }

    /**
     * @return CompressedFile[]
     */
    public function getCompressedFiles()
    {
        $compressedFiles = [];

        $entries = $this->rar->getEntries();

        for ($i = 0; $i < count($entries); $i++) {
            if ($entries[$i]->isDirectory() || $entries[$i]->isEncrypted()) {
                continue;
            }

            $compressedFiles[] = new CompressedFile(
                $i,
                $entries[$i]->getName(),
                $entries[$i]->getUnpackedSize()
            );
        }

        return $compressedFiles;
    }

    protected function extract(CompressedFile $file, $destinationDirectory, $outputFileName)
    {
        $destinationFilePath = $destinationDirectory.$outputFileName;

        $rarEntry = $this->rar->getEntries()[$file->getIndex()];

        $rarEntry->extract(false, $destinationFilePath);
    }

    public static function isAvailable()
    {
        return class_exists('\RarArchive');
    }
}
