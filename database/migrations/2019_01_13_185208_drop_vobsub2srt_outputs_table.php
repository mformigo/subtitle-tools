<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class DropVobsub2srtOutputsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('vobsub2srt_outputs');
    }

    public function down()
    {
        //
    }
}
