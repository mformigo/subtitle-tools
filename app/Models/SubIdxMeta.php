<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubIdxMeta
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $sub_idx_id
 * @property int $sub_file_size
 * @property int $idx_file_size
 * @property int $all_successful
 * @property int $deleted
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereAllSuccessful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereIdxFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereSubFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereSubIdxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxMeta whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubIdxMeta extends Model
{
    protected $guarded = [];
}
