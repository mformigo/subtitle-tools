<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubIdxLanguage extends Model
{
    protected $fillable = ['index', 'language'];

    public function subIdx()
    {
        return $this->belongsTo('App\Models\SubIdx');
    }

}
