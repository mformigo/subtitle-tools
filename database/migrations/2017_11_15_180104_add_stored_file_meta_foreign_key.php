<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoredFileMetaForeignKey extends Migration
{
    public function up()
    {
        Schema::table('stored_file_metas', function (Blueprint $table) {
            $table->integer('stored_file_id')->unsigned()->change();

            $table->foreign('stored_file_id')->references('id')->on('stored_files')->onDelete('cascade');
        });
    }

    public function down()
    {
        //
    }
}
