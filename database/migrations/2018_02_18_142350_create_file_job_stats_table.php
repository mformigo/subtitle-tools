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
            $table->unsignedInteger('times_used')->default(0);
            $table->unsignedInteger('total_files')->default(0);
            $table->unsignedInteger('amount_failed')->default(0);
            $table->unsignedBigInteger('total_size')->default(0);
            $table->timestamps();

            $table->unique(['date', 'tool_route']);
        });
    }
}
