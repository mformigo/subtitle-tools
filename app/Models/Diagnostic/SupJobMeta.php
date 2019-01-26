<?php

namespace App\Models\Diagnostic;

use Illuminate\Database\Eloquent\Model;

class SupJobMeta extends Model
{
    protected $guarded = [];

    protected $casts = [
        'file_size' => 'int',
        'cue_count' => 'int',
    ];
}
