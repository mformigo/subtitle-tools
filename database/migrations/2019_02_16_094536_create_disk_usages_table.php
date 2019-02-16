<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiskUsagesTable extends Migration
{
    public function up()
    {
        Schema::create('disk_usages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('total_size');
            $table->unsignedInteger('total_used');
            $table->unsignedInteger('stored_files_dir_size');
            $table->unsignedInteger('sub_idx_dir_size');
            $table->unsignedInteger('temp_dirs_dir_size');
            $table->unsignedInteger('temp_files_dir_size');
            $table->timestamps();
        });
    }
}
