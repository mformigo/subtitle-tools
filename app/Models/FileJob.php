<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FileJob
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $original_name
 * @property string|null $new_extension
 * @property string|null $error_message
 * @property int $file_group_id
 * @property int $input_stored_file_id
 * @property int|null $output_stored_file_id
 * @property string|null $started_at
 * @property string|null $finished_at
 * @property-read mixed $has_error
 * @property-read mixed $input_file_path
 * @property-read \App\Models\StoredFile $inputStoredFile
 * @property-read \App\Models\StoredFile $outputStoredFile
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereFileGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereInputStoredFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereNewExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereOutputStoredFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileJob whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileJob extends Model
{
    protected $guarded = [];

    public function fileGroup()
    {
        return $this->belongsTo(\App\Models\FileGroup::class);
    }

    public function inputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'input_stored_file_id');
    }

    public function outputStoredFile()
    {
        return $this->hasOne(\App\Models\StoredFile::class, 'id', 'output_stored_file_id');
    }

    public function jobOptions()
    {
        return $this->fileGroup()->job_options;
    }

    public function getHasErrorAttribute()
    {
        return $this->error_message !== null;
    }

    public function getHasFinishedAttribute()
    {
        return $this->finished_at !== null;
    }

    public function getApiValues()
    {
        return [
            'newExtension' => $this->new_extension,
            'originalName' => $this->original_name,
            'isFinished' => $this->has_finished,
            'errorMessage' => $this->has_error ? __($this->error_message) : false,
            'id' => $this->id,
        ];
    }
}
