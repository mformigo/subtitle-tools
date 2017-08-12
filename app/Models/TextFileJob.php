<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TextFileJob
 *
 * @mixin \Eloquent
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $original_file_name
 * @property string $new_file_name
 * @property string $job_options
 * @property string|null $error_message
 * @property int $input_stored_file_id
 * @property int|null $output_stored_file_id
 * @property string $url_key
 * @property string $tool_route
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereInputStoredFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereJobOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereNewFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereOriginalFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereOutputStoredFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereToolRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TextFileJob whereUrlKey($value)
 */
class TextFileJob extends Model
{
    //
}
