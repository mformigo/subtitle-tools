<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimesDownloadedColumn extends Migration
{
    public function up()
    {
        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->unsignedInteger('times_downloaded')->default(0);
        });
    }

    public function down()
    {
        //
    }
}
