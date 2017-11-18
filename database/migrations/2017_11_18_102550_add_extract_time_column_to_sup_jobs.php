<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtractTimeColumnToSupJobs extends Migration
{
    public function up()
    {
        Schema::table('sup_jobs', function (Blueprint $table) {
            $table->integer('extract_time')->nullable()->after('queue_time');
        });
    }

    public function down()
    {
        //
    }
}
