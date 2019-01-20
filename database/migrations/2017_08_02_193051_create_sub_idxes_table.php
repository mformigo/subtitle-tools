<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubIdxesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_idxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url_key')->nullable()->unique();
            $table->string('store_directory');
            $table->string('filename');
            // name of the uploaded sub file without extension
            $table->string('original_name');
            // We can't use StoredFiles for this because they need to be in
            // the same directory, with .sub/.idx extensions
            $table->string('sub_hash');
            $table->string('idx_hash');
            $table->boolean('is_readable')->nullable();
            $table->unsignedInteger('cache_hits')->default(0);
            $table->dateTime('last_cache_hit')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_idxes');
    }
}
