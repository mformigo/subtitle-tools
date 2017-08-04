<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * App\Models\SubIdx
 *
 * @mixin \Eloquent
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 */
class SubIdx extends Model
{
    protected $fillable = ['page_id', 'store_directory', 'filename', 'original_name', 'sub_hash', 'idx_hash'];

    public static function createNewFromUpload(UploadedFile $subFile, UploadedFile $idxFile)
    {
        $subFilePath = $subFile->getRealPath();
        $idxFilePath = $idxFile->getRealPath();

        $subHash = make_file_hash($subFilePath);
        $idxHash = make_file_hash($idxFilePath);

        $baseFileName = substr($subHash, 0, 5) . substr($idxHash, -5);
        $dirName = time() . "-" . $baseFileName;

        $storagePath = storage_path("app/sub-idx/{$dirName}/");

        mkdir($storagePath);

        rename($subFilePath, "{$storagePath}{$baseFileName}.sub");
        rename($idxFilePath, "{$storagePath}{$baseFileName}.idx");

        return SubIdx::create([
            'page_id'         => $baseFileName,
            'store_directory' => $storagePath,
            'filename'        => $baseFileName,
            'original_name' => pathinfo($subFile->getClientOriginalName(), PATHINFO_FILENAME),
            'sub_hash'      => $subHash,
            'idx_hash'      => $idxHash,
        ]);
    }

    public static function isCached($subFilePath, $idxFilePath)
    {
        return SubIdx::where([
            'sub_hash' => make_file_hash($subFilePath),
            'idx_hash' => make_file_hash($idxFilePath),
        ])->count() > 0;
    }

    public static function getCachedPageId($subFilePath, $idxFilePath)
    {
        return SubIdx::where([
            'sub_hash' => make_file_hash($subFilePath),
            'idx_hash' => make_file_hash($idxFilePath),
        ])->firstOrFail()->page_id;
    }

}
