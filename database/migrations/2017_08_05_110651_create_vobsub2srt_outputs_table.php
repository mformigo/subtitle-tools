<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVobsub2srtOutputsTable extends Migration
{
    public function up()
    {
        Schema::create('vobsub2srt_outputs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('sub_idx_id');
            $table->string('argument');
            $table->string('index')->nullable();
            $table->longText('output');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vobsub2srt_outputs');
    }

}
