<?php

namespace App\Http\Controllers\Admin;

use App\Facades\TempFile;
use App\Facades\TextFileIdentifier;
use App\Facades\TextFileReader;
use App\Models\StoredFile;
use Illuminate\Http\Request;

class StoredFilesController extends Controller
{
    public function detail($id)
    {
        $storedFile = StoredFile::findOrFail($id);

        $isTextFile = TextFileIdentifier::isTextFile($storedFile->filePath);

        $lines = $isTextFile ?
            TextFileReader::getLines($storedFile->filePath) :
            ['Not identified as a text file'];

        $meta = $storedFile->meta;

        return view('admin.stored-file-detail', [
            'storedFileId' => $storedFile->id,
            'lines' => $lines,
            'meta' => $meta,
        ]);
    }

    public function download(Request $request)
    {
        $idsString = str_replace(' ', '', trim($request->get('id', '0'), ' ,-'));

        $ids = collect(explode(',', $idsString))->map(function($str) {
           if(!str_contains($str, '-')) {
               return $str;
           }

           $ids = explode('-', $str);

           $from = min($ids[0], $ids[1]);
           $to   = max($ids[0], $ids[1]);

           return range($from, $to);
        })->flatten()->map(function($val) {
            return (string)$val;
        })->all();

        if(count($ids) > 50) {
            return 'You can only download 50 files at once';
        }

        $storedFiles = [];

        foreach($ids as $id) {
            $storedFiles[] = StoredFile::query()->findOrFail($id);
        }

        if(count($storedFiles) === 0) {
            return 'No stored file found with this id';
        }
        else if(count($storedFiles) === 1) {
            return response()->download($storedFiles[0]->filePath, "{$storedFiles[0]->id}.txt");
        }

        $zip = new \ZipArchive();

        $tempFilePath = TempFile::makeFilePath();

        if($zip->open($tempFilePath, \ZipArchive::CREATE) !== true) {
            return 'Could not save the zip';
        }

        foreach($storedFiles as $storedFile) {
            $zip->addFile($storedFile->filePath, "{$storedFile->id}.txt");
        }

        $zip->close();

        return response()->download($tempFilePath, "stored-files.zip");
    }
}
