<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextFileJobsTable extends Migration
{
    public function up()
    {
        Schema::create('text_file_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('original_file_name');
            $table->string('new_extension')->nullable();
            $table->string('job_options');
            $table->string('error_message')->nullable();
            $table->integer('input_stored_file_id');
            $table->integer('output_stored_file_id')->nullable();
            $table->string('url_key');
            $table->string('tool_route');
            $table->dateTime("started_at")->nullable();
            $table->dateTime("finished_at")->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('text_file_jobs');
    }
}
