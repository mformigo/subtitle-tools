<?php

namespace App\Models\Diagnostic;

use Illuminate\Database\Eloquent\Model;

class SupJobMeta extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cue_count' => 'integer',
    ];
}
