<?php

namespace App\Models;

use App\Facades\FileHash;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * App\Models\SubIdx
 *
 * @mixin \Eloquent
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $page_id
 * @property string $store_directory
 * @property string $filename
 * @property string $original_name
 * @property string $sub_hash
 * @property string $idx_hash
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereIdxHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereStoreDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereSubHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereUpdatedAt($value)
 */
class SubIdx extends Model
{
    protected $fillable = ['page_id', 'store_directory', 'filename', 'original_name', 'sub_hash', 'idx_hash'];

    public function getFilePathWithoutExtensionAttribute()
    {
        return $this->store_directory . $this->filename;
    }

    public function isReadable()
    {
        if($this->is_readable !== null) {
            return $this->is_readable;
        }

        $output = $this->execVobsub2srt("--langlist");

        $this->is_readable = !str_contains($output, "Couldn't open VobSub files") && str_contains($output, "Languages:");

        $this->save();

        return $this->is_readable;
    }

    private function execVobsub2srt($argument)
    {
        if(empty($argument)) {
            throw new \Exception("Argument can't be empty");
        }

        return shell_exec("vobsub2srt \"{$this->filePathWithoutExtension}\" {$argument} 2>&1");
    }

    public static function createNewFromUpload(UploadedFile $subFile, UploadedFile $idxFile)
    {
        $subHash = FileHash::make($subFile);
        $idxHash = FileHash::make($idxFile);

        $baseFileName = substr($subHash, 0, 6) . substr($idxHash, 0, 6);

        $storagePath = storage_path("app/sub-idx/" . time() . "-{$baseFileName}/");

        mkdir($storagePath);
        rename($subFile->getRealPath(), "{$storagePath}{$baseFileName}.sub");
        rename($idxFile->getRealPath(), "{$storagePath}{$baseFileName}.idx");

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
            'sub_hash' => FileHash::make($subFilePath),
            'idx_hash' => FileHash::make($idxFilePath),
        ])->count() > 0;
    }

    public static function getFromCache($subFilePath, $idxFilePath)
    {
        return SubIdx::where([
            'sub_hash' => FileHash::make($subFilePath),
            'idx_hash' => FileHash::make($idxFilePath),
        ])->firstOrFail();
    }

}
