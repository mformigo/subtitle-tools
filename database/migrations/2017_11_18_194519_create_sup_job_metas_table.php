<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupJobMetasTable extends Migration
{
    public function up()
    {
        Schema::create('sup_job_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sup_job_id')->unsigned()->unique();
            $table->string('format');
            $table->integer('cue_count');
            $table->timestamps();

            $table->foreign('sup_job_id')->references('id')->on('sup_jobs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sup_job_metas');
    }
}
