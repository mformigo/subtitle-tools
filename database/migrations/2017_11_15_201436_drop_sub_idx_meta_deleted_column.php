<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSubIdxMetaDeletedColumn extends Migration
{
    public function up()
    {
        Schema::table('sub_idx_metas', function (Blueprint $table) {
            $table->dropColumn('deleted');
        });
    }

    public function down()
    {
        //
    }
}
