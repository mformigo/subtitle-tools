<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vobsub2srtOutput
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $sub_idx_id
 * @property string $argument
 * @property string|null $index
 * @property string $output
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereArgument($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereOutput($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereSubIdxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vobsub2srtOutput whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Vobsub2srtOutput extends Model
{
    protected $fillable = ['argument', 'index', 'output'];
}
