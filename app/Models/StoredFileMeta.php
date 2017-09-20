<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StoredFileMeta
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $stored_file_id
 * @property int $size
 * @property string $mime
 * @property bool $is_text_file
 * @property string|null $encoding
 * @property string|null $identified_as
 * @property string|null $line_endings
 * @property int $line_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereEncoding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereIdentifiedAs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereIsTextFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereLineCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereLineEndings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereStoredFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\StoredFileMeta whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoredFileMeta extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_text_file' => 'boolean',
        'line_count' => 'integer',
        'size' => 'integer',
    ];
}
