<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('sup_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url_key')->unique();
            $table->timestamps();
        });


        Schema::create('sup_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sup_group_id')->unsigned();
            $table->string('original_name');
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

            $table->foreign('sup_group_id')->references('id')->on('sup_groups')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sup_groups');
        Schema::dropIfExists('sup_jobs');
    }
}
