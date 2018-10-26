<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileJob;
use App\Support\Facades\TempFile;
use App\Models\StoredFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StoredFilesController
{
    public function detail(StoredFile $storedFile)
    {
         $relatedFileJobs = FileJob::query()
             ->where(function (Builder $query) use ($storedFile) {
                 $query->where('input_stored_file_id', $storedFile->id)->orWhere('output_stored_file_id', $storedFile->id);
             })
             ->get();

        $lines = is_text_file($storedFile)
            ? read_lines($storedFile)
            : ['Not identified as a text file'];

        return view('admin.stored-file-detail', [
            'storedFileId' => $storedFile->id,
            'lines' => $lines,
            'meta' => $storedFile->meta,
            'relatedFileJobs' => $relatedFileJobs,
        ]);
    }

    public function download(Request $request)
    {
        $idsString = str_replace(' ', '', trim($request->get('id', '0'), ' ,-'));

        $ids = collect(explode(',', $idsString))->map(function ($str) {
               if (!str_contains($str, '-')) {
                   return $str;
               }

               $ids = explode('-', $str);

               $from = min($ids[0], $ids[1]);
               $to   = max($ids[0], $ids[1]);

               return range($from, $to);
            })
            ->flatten()
            ->map(function ($val) {
                return (string) $val;
            })
            ->all();

        if (count($ids) > 50) {
            return 'You can only download 50 files at once';
        }

        $storedFiles = [];

        foreach ($ids as $id) {
            $storedFiles[] = StoredFile::query()->findOrFail($id);
        }

        if (count($storedFiles) === 0) {
            return 'No stored file found with this id';
        } elseif (count($storedFiles) === 1) {
            return response()->download($storedFiles[0]->filePath, "{$storedFiles[0]->id}.txt");
        }

        $zip = new \ZipArchive();

        $tempFilePath = TempFile::makeFilePath();

        if ($zip->open($tempFilePath, \ZipArchive::CREATE) !== true) {
            return 'Could not save the zip';
        }

        foreach ($storedFiles as $storedFile) {
            $zip->addFile($storedFile->filePath, "{$storedFile->id}.txt");
        }

        $zip->close();

        return response()->download($tempFilePath, "stored-files.zip");
    }
}
