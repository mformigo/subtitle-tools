<?php

use App\Models\SubIdx;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastCacheHitColumnToSubidxTable extends Migration
{
    public function up()
    {
        Schema::table('sub_idxes', function (Blueprint $table) {
            $table->unsignedInteger('cache_hits')->default(0);
            $table->dateTime('last_cache_hit')->nullable();
        });

        SubIdx::query()->update([
            'last_cache_hit' => now()->subDays(30),
        ]);
    }

    public function down()
    {
        Schema::table('sub_idxes', function (Blueprint $table) {
            $table->dropColumn('cache_hits');
            $table->dropColumn('last_cache_hit');
        });
    }
}
