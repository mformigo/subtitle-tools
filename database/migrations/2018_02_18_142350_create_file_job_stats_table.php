<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileJobStatsTable extends Migration
{
    public function up()
    {
        Schema::create('file_job_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->string('tool_route');
            $table->integer('times_used')->unsigned()->default(0);
            $table->integer('total_files')->unsigned()->default(0);
            $table->integer('amount_failed')->unsigned()->default(0);
            $table->bigInteger('total_size')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['date', 'tool_route']);
        });
    }
}
