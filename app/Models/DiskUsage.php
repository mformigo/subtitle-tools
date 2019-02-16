<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiskUsage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total_size' => 'int',
        'total_used' => 'int',
        'stored_files_dir_size' => 'int',
        'sub_idx_dir_size' => 'int',
        'temp_dirs_dir_size' => 'int',
        'temp_files_dir_size' => 'int',
    ];

    public function getTotalUsagePercentageAttribute()
    {
        if ($this->total_size === 0) {
            return 0;
        }

        return (int) round(($this->total_used / $this->total_size) * 100);
    }
}
