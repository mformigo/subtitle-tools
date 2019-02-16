<?php

namespace App\Models;

use App\Support\Facades\FileHash;
use App\Support\Facades\TempFile;
use App\Subtitles\TextFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StoredFile extends Model
{
    protected $guarded = [];

    public function meta()
    {
        return $this->hasOne(StoredFileMeta::class);
    }

    public function getFilePathAttribute()
    {
        return storage_disk_file_path($this->storage_file_path);
    }

    public static function getOrCreate($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $hash = FileHash::make($filePath);

        $fromCache = StoredFile::where('hash', $hash);

        if ($fromCache->count() > 0) {
            return $fromCache->first();
        }

        $storagePath = 'stored-files/'.now()->format('Y-W/z');

        if (!File::isDirectory($storagePath)) {
            Storage::makeDirectory($storagePath);
        }

        $storageFilePath = "{$storagePath}/" . now()->format('U') . "-" . substr($hash, 0, 16);

        // copy instead of moving to prevent from moving test files
        copy($filePath, storage_disk_file_path($storageFilePath));

        return StoredFile::create([
            'storage_file_path' => $storageFilePath,
            'hash' => $hash,
        ]);
    }

    public static function createFromTextFile(TextFile $textFile)
    {
        $filePath = TempFile::make("\xEF\xBB\xBF".$textFile->getContent());

        return self::getOrCreate($filePath);
    }
}
