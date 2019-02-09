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
            $table->unsignedInteger('file_group_id');
            $table->string('original_name', 2000);
            $table->string('new_extension')->nullable();
            $table->string('error_message')->nullable();
            $table->unsignedInteger('input_stored_file_id');
            $table->unsignedInteger('output_stored_file_id')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();

            $table->foreign('file_group_id')->references('id')->on('file_groups')->onDelete('cascade');
            $table->foreign('input_stored_file_id')->references('id')->on('stored_files');
            $table->foreign('output_stored_file_id')->references('id')->on('stored_files');
        });
    }
}
