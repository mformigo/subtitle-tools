<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSubIdxMetaTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('sub_idx_metas');

        Schema::table('sub_idxes', function (Blueprint $table) {
             $table->unsignedInteger('sub_file_size')->default(0);
             $table->unsignedInteger('idx_file_size')->default(0);
        });

        Schema::table('sup_job_metas', function (Blueprint $table) {
            $table->unsignedInteger('file_size')->default(0);
        });
    }
}
