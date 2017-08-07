<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubIdxLanguage
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $sub_idx_id
 * @property string $index
 * @property string $language
 * @property string|null $filename
 * @property string|null $error
 * @property string|null $finished_at
 * @property-read \App\Models\SubIdx $subIdx
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereSubIdxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubIdxLanguage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubIdxLanguage extends Model
{
    protected $fillable = ['index', 'language', 'filename', 'has_error', 'started_at', 'finished_at'];

    public function subIdx()
    {
        return $this->belongsTo('App\Models\SubIdx');
    }

}
