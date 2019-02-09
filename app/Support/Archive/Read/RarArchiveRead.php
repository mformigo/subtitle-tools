<?php

namespace App\Support\Archive\Read;

use RarArchive;
use App\Support\Archive\CompressedFile;

class RarArchiveRead extends ArchiveRead
{
    protected static $archiveMimeType = 'application/x-rar';

    protected $rar;

    // temp hack to catch an broken rar file.
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;

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

        // This try/catch is a temporary hack to catch a rar file that throws a "ERAR_BAD_DATA" exception.
        try {
            $rarEntry->extract(false, $destinationFilePath);
        } catch (\Exception $e) {
            copy($this->filePath, $aaa = storage_path('rar-error-'.now()->format('U').'.rar'));

            info($aaa);

            throw $e;
        }
    }

    public static function isAvailable()
    {
        return class_exists('\RarArchive');
    }
}
