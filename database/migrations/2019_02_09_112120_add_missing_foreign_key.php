<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingForeignKey extends Migration
{
    public function up()
    {
        DB::transaction(function () {
//            Schema::table('file_jobs', function (Blueprint $table) {
//                $table->foreign('input_stored_file_id')->references('id')->on('stored_files');
//                $table->foreign('output_stored_file_id')->references('id')->on('stored_files');
//            });

//            Schema::table('sub_idx_languages', function (Blueprint $table) {
//                $table->foreign('output_stored_file_id')->references('id')->on('stored_files');
//            });

            Schema::table('stored_files', function (Blueprint $table) {
                $table->unique('hash');
            });

            Schema::table('file_groups', function (Blueprint $table) {
                $table->foreign('archive_stored_file_id')->references('id')->on('stored_files');
            });

            Schema::table('sup_jobs', function (Blueprint $table) {
                $table->foreign('input_stored_file_id')->references('id')->on('stored_files');
                $table->foreign('output_stored_file_id')->references('id')->on('stored_files');

                $table->unique(['ocr_language', 'input_file_hash']);
            });

            Schema::table('sup_job_metas', function (Blueprint $table) {
                $table->unique('sup_job_id');
            });
        });
    }
}
