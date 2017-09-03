<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoredFileMeta extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_text_file' => 'boolean',
        'line_count' => 'integer',
        'size' => 'integer',
    ];
}
