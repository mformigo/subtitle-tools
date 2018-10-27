<?php

use App\Models\StoredFile;
use App\Models\SupJob;
use Faker\Generator as Faker;

$factory->define(SupJob::class, function (Faker $faker) {
    $inputStoredFile = factory(StoredFile::class)->create();

    return [
        'url_key' => generate_url_key(),
        'original_name' => snake_case($faker->words(3, true)).'.sup',
        'ocr_language' => $faker->randomElement(config('st.tesseract.languages')),
        'input_stored_file_id' => $inputStoredFile->id,
        'input_file_hash' => $inputStoredFile->hash,
        'output_stored_file_id' => factory(StoredFile::class)->create()->id,
        'error_message' => null,
        'internal_error_message' => null,
        'temp_dir' => null,
        'started_at' => null,
        'finished_at' => null,
        'queue_time' => null,
        'extract_time' => null,
        'work_time' => null,
        'last_cache_hit' => null,
        'cache_hits' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
