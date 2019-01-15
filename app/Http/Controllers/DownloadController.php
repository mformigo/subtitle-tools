<?php

namespace App\Http\Controllers;

use App\Models\FileGroup;

class DownloadController
{
    public function fileGroupArchive($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->whereNotNull('archive_stored_file_id')
            ->firstOrFail();

        $storedArchiveFile = $fileGroup->archiveStoredFile;

        return response()->download($storedArchiveFile->filePath, 'subtitletools-archive.zip');
    }
}
