<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupJobsTable extends Migration
{
    public function up()
    {
        Schema::create('sup_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url_key')->unique();
            $table->string('original_name');
            $table->string('ocr_language');
            $table->integer('input_stored_file_id')->unsigned();
            $table->integer('output_stored_file_id')->unsigned()->nullable();
            $table->string('error_message')->nullable();
            $table->string('internal_error_message')->nullable();
            $table->string('temp_dir')->nullable();

            $table->timestamps();

            // MeasuresQueueTime trait
            $table->dateTime("started_at")->nullable();
            $table->dateTime("finished_at")->nullable();
            $table->integer('queue_time')->nullable();
            $table->integer('work_time')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sup_jobs');
    }
}
