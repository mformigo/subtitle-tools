<?php

namespace App\Http\Controllers\Admin;

use App\Facades\TextFileReader;
use App\Models\StoredFile;

class StoredFilesController extends Controller
{
    public function detail($id)
    {
        $file = StoredFile::findOrFail($id);

        $lines = TextFileReader::getLines($file->filePath);

        return implode('<br />', $lines);
    }
}
