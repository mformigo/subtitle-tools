<?php

namespace App\Models;

use App\Facades\FileHash;
use App\Jobs\ExtractSubIdxLanguage;
use App\Utils\IdxFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

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
 * @property int|null $is_readable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdx whereIsReadable($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubIdxLanguage[] $languages
 */
class SubIdx extends Model
{
    protected $fillable = ['page_id', 'store_directory', 'filename', 'original_name', 'sub_hash', 'idx_hash'];

    protected function getFilePathWithoutExtensionAttribute()
    {
        return $this->store_directory . $this->filename;
    }

    public function languages()
    {
        return $this->hasMany('App\Models\SubIdxLanguage');
    }

    protected function makeLanguageExtractJobs()
    {
        if($this->is_readable !== null) {
            throw new \Exception("Jobs have already been made for this SubIdx");
        }

        $outputLines = preg_split("/\r\n|\n|\r/", $this->execVobsub2srt("--langlist"));

        file_put_contents("{$this->store_directory}vobsub2srt-langlist-output.txt", implode("\r\n", $outputLines));

        if(!in_array("Languages:", $outputLines) || in_array("Couldn't open VobSub files", $outputLines)) {
            $this->is_readable = false;
            $this->save();
            return;
        }

        $idxLanguages = new IdxFile("{$this->filePathWithoutExtension}.idx");

        foreach($outputLines as $line) {
            if(preg_match('/^(?<index>\d+): ([a-z]+|\(no id\))$/', $line, $match)) {
                $subIdxLanguage = new SubIdxLanguage([
                   'index'    => $match['index'],
                   'language' => $idxLanguages->getLanguageForIndex($match['index']),
                ]);

                $this->languages()->save($subIdxLanguage);

                dispatch((new ExtractSubIdxLanguage($subIdxLanguage))->onQueue('sub-idx'));
            }
        }

        $this->is_readable = true;
        $this->save();
    }

    private function execVobsub2srt($argument)
    {
        if(empty($argument)) {
            throw new \Exception("Argument can't be empty");
        }

        return shell_exec("vobsub2srt \"{$this->filePathWithoutExtension}\" {$argument} 2>&1");
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

        $storagePath = storage_path("app/sub-idx/" . time() . "-{$baseFileName}/");

        mkdir($storagePath);
        rename($subFile->getRealPath(), "{$storagePath}{$baseFileName}.sub");
        rename($idxFile->getRealPath(), "{$storagePath}{$baseFileName}.idx");

        $subIdx = SubIdx::create([
            'original_name'   => pathinfo($subFile->getClientOriginalName(), PATHINFO_FILENAME),
            'store_directory' => $storagePath,
            'filename' => $baseFileName,
            'page_id'  => $baseFileName,
            'sub_hash' => $subHash,
            'idx_hash' => $idxHash,
        ]);

        $subIdx->makeLanguageExtractJobs();

        return $subIdx;
    }

}
