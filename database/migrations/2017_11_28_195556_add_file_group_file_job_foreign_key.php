<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileGroupFileJobForeignKey extends Migration
{
    public function up()
    {
        Schema::table('file_jobs', function (Blueprint $table) {
            $table->integer('file_group_id')->unsigned()->change();

            $table->foreign('file_group_id')->references('id')->on('file_groups')->onDelete('cascade');
        });
    }

    public function down()
    {
        //
    }
}
