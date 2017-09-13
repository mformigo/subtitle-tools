<?php

use App\Models\SubIdxLanguage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubidxQueueTimeAndExtractTimeColumns extends Migration
{
    public function up()
    {
        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->integer('queue_time')->nullable();
            $table->integer('extract_time')->nullable();
            $table->boolean('timed_out')->nullable();
        });

        $all = SubIdxLanguage::all();

        foreach($all->all() as $subIdxLanguage) {
            if(!$subIdxLanguage->hasStarted) {
                continue;
            }

            $created = new Carbon($subIdxLanguage->created_at);
            $start = new Carbon($subIdxLanguage->started_at);

            $subIdxLanguage->queue_time = $start->diffInSeconds($created);

            if($subIdxLanguage->hasFinished) {
                $end = new Carbon($subIdxLanguage->finished_at);

                $subIdxLanguage->extract_time = $end->diffInSeconds($start);

                $output = $subIdxLanguage->vobsubOutput()->output ?? 'NO OUTPUT';

                $subIdxLanguage->timed_out = stripos($output, '__error: timeout') !== false;
            }

            $subIdxLanguage->save();
        }

    }

    public function down()
    {
        Schema::table('sub_idx_languages', function (Blueprint $table) {
            $table->dropColumn([
                'queue_time',
                'extract_time',
                'timed_out'
            ]);
        });
    }
}
