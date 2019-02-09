<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoreForeignKeys extends Migration
{
    public function up()
    {
        Schema::table('sub_idxes', function (Blueprint $table) {
            $table->unique(['sub_hash', 'idx_hash']);
        });

        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->unique(['sub_idx_id', 'index']);
        });

        Schema::table('file_groups', function (Blueprint $table) {
            $table->unique('url_key');
        });
    }
}
