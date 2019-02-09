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
            $table->unsignedInteger('sub_idx_id');
            $table->string('index');
            $table->string('language');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('queued_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->unsignedInteger('output_stored_file_id')->nullable();
            $table->string('error_message')->nullable();
            $table->unsignedInteger('times_downloaded')->default(0);
            $table->dateTime('updated_at')->nullable();

            $table->foreign('sub_idx_id')->references('id')->on('sub_idxes')->onDelete('cascade');
            $table->foreign('output_stored_file_id')->references('id')->on('stored_files');

            $table->unique(['sub_idx_id', 'index']);
        });
    }
}
