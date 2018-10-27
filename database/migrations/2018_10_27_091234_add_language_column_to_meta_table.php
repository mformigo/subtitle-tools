<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageColumnToMetaTable extends Migration
{
    public function up()
    {
        Schema::table('stored_file_metas', function (Blueprint $table) {
            $table->string('language')->nullable()->after('line_count');
        });
    }

    public function down()
    {
        Schema::table('stored_file_metas', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
}
