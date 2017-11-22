<?php

namespace App\Models;

use App\Support\Facades\FileHash;
use App\Jobs\ExtractSubIdxLanguageJob;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
 * @property string $filePathWithoutExtension
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereIdxHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereStoreDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereSubHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereUpdatedAt($value)
 * @property int|null $is_readable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereIsReadable($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubIdxLanguage[] $languages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vobsub2srtOutput[] $vobsub2srtOutputs
 * @property-read \App\Models\SubIdxMeta $meta
 */
class SubIdx extends Model
{
    protected $fillable = ['page_id', 'store_directory', 'filename', 'original_name', 'sub_hash', 'idx_hash', 'is_readable'];

    protected function getFilePathWithoutExtensionAttribute()
    {
        return storage_disk_file_path($this->store_directory . $this->filename);
    }

    public function languages()
    {
        return $this->hasMany('App\Models\SubIdxLanguage');
    }

    public function vobsub2srtOutputs()
    {
        return $this->hasMany('App\Models\Vobsub2srtOutput');
    }

    public function meta()
    {
        return $this->hasOne(\App\Models\SubIdxMeta::class);
    }

    /**
     * @return VobSub2SrtInterface
     */
    public function getVobSub2Srt()
    {
        return app(VobSub2SrtInterface::class, ['path' => $this->filePathWithoutExtension, 'subIdx' => $this]);
    }

    private function makeLanguageExtractJobs()
    {
        $languages = $this->getVobSub2Srt()->getLanguages();

        foreach($languages as $language) {
            $subIdxLanguage = $this->languages()->create($language);

            ExtractSubIdxLanguageJob::dispatch($subIdxLanguage)->onQueue('sub-idx');
        }
    }

    public static function getOrCreateFromUpload(UploadedFile $subFile, UploadedFile $idxFile)
    {
        $subHash = FileHash::make($subFile);
        $idxHash = FileHash::make($idxFile);

        $fromCache = SubIdx::where(['sub_hash' => $subHash, 'idx_hash' => $idxHash]);

        if($fromCache->count() > 0) {
            return $fromCache->first();
        }

        $baseFileName = substr($subHash, 0, 6) . substr($idxHash, 0, 6);

        $storagePath = "sub-idx/" . date('Y-W') . '/' . time() . "-{$baseFileName}/";

        Storage::makeDirectory($storagePath);
        // copy instead of moving to prevent from moving test files
        copy($subFile->getRealPath(), storage_disk_file_path("{$storagePath}{$baseFileName}.sub"));
        copy($idxFile->getRealPath(), storage_disk_file_path("{$storagePath}{$baseFileName}.idx"));

        $subIdx = SubIdx::create([
            'original_name'   => pathinfo($subFile->getClientOriginalName(), PATHINFO_FILENAME),
            'store_directory' => $storagePath,
            'filename' => $baseFileName,
            'sub_hash' => $subHash,
            'idx_hash' => $idxHash,
            'is_readable' => false,
        ]);

        $subIdx->makeLanguageExtractJobs();

        if($subIdx->languages->count() > 0) {
            $subIdx->is_readable = true;
            $subIdx->page_id = $baseFileName;
            $subIdx->save();
        }

        return $subIdx;
    }

}
