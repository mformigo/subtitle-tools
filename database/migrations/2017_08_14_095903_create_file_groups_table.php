<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('file_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('tool_route');
            $table->string('url_key');
            $table->string('job_options')->default('{}');
            $table->dateTime("file_jobs_finished_at")->nullable();
            $table->dateTime("archive_requested_at")->nullable();
            $table->dateTime("archive_finished_at")->nullable();
            $table->string('archive_error')->nullable();
            $table->integer('archive_stored_file_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_groups');
    }
}
