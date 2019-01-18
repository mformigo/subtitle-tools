<?php

namespace App\Models;

use App\Support\Facades\FileHash;
use App\Jobs\ExtractSubIdxLanguageJob;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubIdx extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_cache_hit' => 'datetime',
        'cache_hits' => 'int',
    ];

    protected function getFilePathWithoutExtensionAttribute()
    {
        return storage_disk_file_path($this->store_directory.$this->filename);
    }

    public function languages()
    {
        return $this->hasMany(SubIdxLanguage::class);
    }

    public function meta()
    {
        return $this->hasOne(SubIdxMeta::class);
    }

    /**
     * @return VobSub2SrtInterface
     */
    public function getVobSub2Srt()
    {
        return app(VobSub2SrtInterface::class, [
            'path' => $this->file_path_without_extension,
            'subIdx' => $this,
        ]);
    }

    public function makeLanguageExtractJobs()
    {
        $languages = $this->getVobSub2Srt()->getLanguages();

        foreach ($languages as $language) {
            $subIdxLanguage = $this->languages()->create($language);

            ExtractSubIdxLanguageJob::dispatch($subIdxLanguage)->onQueue('sub-idx');
        }
    }

    public static function getOrCreateFromUpload(UploadedFile $subFile, UploadedFile $idxFile)
    {
        $subHash = FileHash::make($subFile);
        $idxHash = FileHash::make($idxFile);

        $cachedSubIdx = SubIdx::query()
            ->where('sub_hash', $subHash)
            ->where('idx_hash', $idxHash)
            ->first();

        if ($cachedSubIdx) {
            $cachedSubIdx->update([
                'last_cache_hit' => now(),
                'cache_hits' => $cachedSubIdx->cache_hits + 1,
            ]);

            return $cachedSubIdx;
        }

        $baseFileName = substr($subHash, 0, 6).substr($idxHash, 0, 6);

        // The date in this path is used in the "PruneSubIdxFiles" command
        $storagePath = 'sub-idx/'.date('Y-z').'/'.time()."-{$baseFileName}/";

        Storage::makeDirectory($storagePath);

        // copy instead of moving to prevent from moving test files
        copy($subFile->getRealPath(), storage_disk_file_path($storagePath.$baseFileName.'.sub'));
        copy($idxFile->getRealPath(), storage_disk_file_path($storagePath.$baseFileName.'.idx'));

        $subIdx = SubIdx::create([
            'original_name'   => pathinfo($subFile->getClientOriginalName(), PATHINFO_FILENAME),
            'store_directory' => $storagePath,
            'filename'        => $baseFileName,
            'sub_hash'        => $subHash,
            'idx_hash'        => $idxHash,
            'is_readable'     => false,
        ]);

        $subIdx->makeLanguageExtractJobs();

        if ($subIdx->languages->count() > 0) {
            $subIdx->update([
                'is_readable' => true,
                'url_key' => generate_url_key(),
            ]);
        }

        return $subIdx;
    }
}
