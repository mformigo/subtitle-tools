<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileJobsTable extends Migration
{
    public function up()
    {
        Schema::create('file_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('original_name');
            $table->string('new_extension')->nullable();
            $table->string('error_message')->nullable();
            $table->integer('file_group_id');
            $table->integer('input_stored_file_id');
            $table->integer('output_stored_file_id')->nullable();
            $table->dateTime("started_at")->nullable();
            $table->dateTime("finished_at")->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_jobs');
    }
}
