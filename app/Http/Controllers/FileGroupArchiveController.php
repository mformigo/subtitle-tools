<?php

namespace App\Http\Controllers;

use App\Models\FileGroup;

class FileGroupArchiveController
{
    public function download($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->whereNotNull('archive_stored_file_id')
            ->firstOrFail();

        $storedArchiveFile = $fileGroup->archiveStoredFile;

        return response()->download($storedArchiveFile->file_path, 'subtitletools-archive.zip');
    }

    public function downloadRedirect($urlKey)
    {
        $fileGroup = FileGroup::where('url_key', $urlKey)->firstOrFail();

        return redirect($fileGroup->result_route);
    }
}
