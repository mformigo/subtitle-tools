<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupInputFileHashColumn extends Migration
{
    public function up()
    {
        \App\Models\SupJob::query()->delete();

        Schema::table('sup_jobs', function (Blueprint $table) {
            $table->string('input_file_hash')->after('input_stored_file_id');
            $table->integer('input_stored_file_id')->nullable()->unsigned()->change();
        });
    }

    public function down()
    {

    }
}
