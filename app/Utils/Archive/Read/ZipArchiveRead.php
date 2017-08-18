<?php

namespace App\Utils\Archive\Read;

use App\Facades\FileHash;
use App\Utils\Archive\CompressedFile;
use Illuminate\Support\Facades\App;

class ZipArchiveRead implements ArchiveReadInterface
{
    protected $isSuccessfullyOpened = false;

    protected $zip;

    public function __construct($filePath)
    {
        $this->zip = new \ZipArchive();

        if($this->zip->open($filePath) === true) {
            $this->isSuccessfullyOpened = true;
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
        return $this->zip->numFiles;
    }

    /**
     * @return CompressedFile[]
     */
    public function getFiles()
    {
        $compressedFiles = [];

        for($i = 0; $i < $this->zip->numFiles; $i++) {
            $stat = $this->zip->statIndex($i);

            // Skip directories
            if(strlen($stat["name"]) === 0 || substr($stat["name"], -1) === DIRECTORY_SEPARATOR) {
                continue;
            }

            $newFile = new CompressedFile();

            $newFile->setName($stat['name'])
                ->setRealSize($stat["size"]);

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

        $this->zip->renameName($file->getName(), $newName);

        $this->zip->extractTo($destinationDirectory, [$newName]);

        if(App::environment('testing')) {
            $this->zip->unchangeName($newName);
        }

        return $destinationDirectory . DIRECTORY_SEPARATOR . $newName;
    }

    public static function isThisFormat($filePath, $strict = true)
    {
        if(!$strict) {
            return ends_with(strtolower($filePath), '.zip');
        }

        if(file_mime($filePath) === 'application/zip') {
            $archiveRead = new ZipArchiveRead($filePath);

            return $archiveRead !== null && $archiveRead->isSuccessfullyOpened();
        }

        // empty zip files return an 'application/octet-stream' mime, so they are matched by hash
        $emptyArchivesHashes = [
            'b04f3ee8f5e43fa3b162981b50bb72fe1acabb33',
        ];

        if(in_array(FileHash::make($filePath), $emptyArchivesHashes)) {
            return true;
        }

        return false;
    }
}