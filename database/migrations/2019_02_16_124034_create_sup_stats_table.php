<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupStatsTable extends Migration
{
    public function up()
    {
        Schema::create('sup_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->unique();
            $table->unsignedInteger('bluray_sup_count')->default(0);
            $table->unsignedInteger('hddvd_sup_count')->default(0);
            $table->unsignedInteger('dvd_sup_count')->default(0);
            $table->unsignedBigInteger('total_size')->default(0);
            $table->unsignedInteger('images_ocrd_count')->default(0);
            $table->unsignedBigInteger('milliseconds_spent_ocring')->default(0);
            $table->timestamps();
        });
    }
}
