<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileGroup extends Model
{
    protected $guarded = [];

    public function fileJobs()
    {
        return $this->hasMany(\App\Models\FileJob::class);
    }

    public function getResultRouteAttribute()
    {
        return route("{$this->tool_route}-result", ['urlKey' => $this->url_key]);
    }
}
