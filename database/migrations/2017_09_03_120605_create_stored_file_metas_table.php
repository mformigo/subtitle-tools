<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoredFileMetasTable extends Migration
{
    public function up()
    {
        Schema::create('stored_file_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stored_file_id')->unique();
            $table->unsignedInteger('size');
            $table->string('mime');
            $table->boolean('is_text_file')->default(false);
            $table->string('encoding')->nullable();
            $table->string('identified_as')->nullable();
            $table->string('line_endings')->nullable();
            $table->integer('line_count')->nullable();
            $table->string('language')->nullable();
            $table->timestamps();

            $table->foreign('stored_file_id')->references('id')->on('stored_files')->onDelete('cascade');
        });
    }
}
