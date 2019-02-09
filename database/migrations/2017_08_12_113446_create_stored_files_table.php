<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoredFilesTable extends Migration
{
    public function up()
    {
        Schema::create('stored_files', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('hash');
            $table->string('storage_file_path');
        });
    }
}
