<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('file_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('original_name')->nullable();
            $table->string('tool_route');
            $table->string('url_key');
            $table->string('job_options')->default('{}');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_groups');
    }
}
