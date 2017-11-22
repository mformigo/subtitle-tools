<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubIdxForeignKey extends Migration
{
    public function up()
    {
        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->integer('sub_idx_id')->unsigned()->change();

            $table->foreign('sub_idx_id')->references('id')->on('sub_idxes')->onDelete('cascade');
        });
    }

    public function down()
    {
        //
    }
}
