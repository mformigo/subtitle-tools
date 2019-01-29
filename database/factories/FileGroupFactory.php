<?php

use App\Models\FileGroup;
use Faker\Generator as Faker;

$factory->define(FileGroup::class, function (Faker $faker) {
    return [
        'tool_route' => collect(config('st.tool_routes'))->random(),
        'url_key' => generate_url_key(),
        'job_options' => [],
        'file_jobs_finished_at' => now(),
        'archive_requested_at' => null,
        'archive_finished_at' => null,
        'archive_error' => null,
        'archive_stored_file_id' => null,
        'created_at' => now()->subSeconds(5),
        'updated_at' => now(),
    ];
});
