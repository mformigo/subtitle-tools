<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubIdxLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_idx_languages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('sub_idx_id');
            $table->string('index');
            $table->string('language');
            $table->integer('output_stored_file_id')->nullable();
            $table->string('error_message')->nullable();
            $table->dateTime("started_at")->nullable();
            $table->dateTime("finished_at")->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_idx_languages');
    }
}
