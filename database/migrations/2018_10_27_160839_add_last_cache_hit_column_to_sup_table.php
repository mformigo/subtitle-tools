<?php

use App\Models\SupJob;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastCacheHitColumnToSupTable extends Migration
{
    public function up()
    {
        Schema::table('sup_jobs', function (Blueprint $table) {
            $table->unsignedInteger('cache_hits')->default(0);
            $table->dateTime('last_cache_hit')->nullable();
        });

        SupJob::query()->update([
            'last_cache_hit' => now()->subDays(30),
        ]);
    }

    public function down()
    {
        Schema::table('sup_jobs', function (Blueprint $table) {
            $table->dropColumn('cache_hits');
            $table->dropColumn('last_cache_hit');
        });
    }
}
