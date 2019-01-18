<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubIdxTable extends Migration
{
    public function up()
    {
        Schema::table('sub_idxes', function (Blueprint $table) {
            $table->renameColumn('page_id', 'url_key');
        });

        Schema::table('sub_idxes', function (Blueprint $table) {
            $table->unique('url_key');
        });
    }

    public function down()
    {
        //
    }
}
