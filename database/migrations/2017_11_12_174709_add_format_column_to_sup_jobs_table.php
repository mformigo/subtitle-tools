<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormatColumnToSupJobsTable extends Migration
{
    public function up()
    {
        Schema::table('sup_jobs', function (Blueprint $table) {
            $table->string('format')->nullable()->after('input_stored_file_id');
        });
    }

    public function down()
    {
        Schema::table('sup_jobs', function (Blueprint $table) {
            $table->dropColumn('format');
        });
    }
}
