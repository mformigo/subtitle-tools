<?php

use App\Models\SubIdxLanguage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubIdxLanguageTable extends Migration
{
    public function up()
    {
        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->dateTime('queued_at')->nullable();
        });

        SubIdxLanguage::each(function (SubIdxLanguage $language) {
            $language->update(['queued_at' => $language->started_at]);
        });

        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->dropColumn('queue_time');
        });

        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->dropColumn('extract_time');
        });

        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->dropColumn('timed_out');
        });
    }

    public function down()
    {
        //
    }
}
