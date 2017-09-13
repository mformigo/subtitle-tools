<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubIdxMetasTable extends Migration
{
    public function up()
    {
        Schema::create('sub_idx_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('sub_idx_id');
            $table->integer('sub_file_size');
            $table->integer('idx_file_size');
            $table->boolean('all_successful');
            $table->boolean('deleted');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_idx_metas');
    }
}
