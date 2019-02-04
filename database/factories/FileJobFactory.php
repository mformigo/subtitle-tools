<?php

use App\Models\FileJob;
use App\Models\StoredFile;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(FileJob::class, function (Faker $faker) {
    return [
        'original_name' => Str::slug($faker->words(mt_rand(1, 6), true)).'.srt',
        'new_extension' => 'srt',
        'error_message' => null,
        'input_stored_file_id' => factory(StoredFile::class)->create()->id,
        'output_stored_file_id' => factory(StoredFile::class)->create()->id,
        'started_at' => now()->subSeconds(4),
        'finished_at' => now()->subSeconds(3),
        'created_at' => now()->subSeconds(5),
        'updated_at' => now(),
    ];
});
