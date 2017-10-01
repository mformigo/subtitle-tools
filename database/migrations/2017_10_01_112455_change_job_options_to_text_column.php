<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeJobOptionsToTextColumn extends Migration
{
    public function up()
    {
        Schema::table('file_groups', function (Blueprint $table) {
            $table->text('job_options')->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
