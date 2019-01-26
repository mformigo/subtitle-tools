<?php

use App\Models\Diagnostic\SupJobMeta;
use Faker\Generator as Faker;

$factory->define(SupJobMeta::class, function (Faker $faker) {
    return [
        'file_size' => mt_rand(100, 10000000),
        'format' => $faker->randomElement([
            'BluraySup',
            'BluraySup',
            'BluraySup',
            'DvdSup',
            'DvdSup',
            'HddvdSup',
        ]),
        'failed_to_open' => $failedToOpen = $faker->boolean(20),
        'cue_count' => $failedToOpen ? null : mt_rand(1, 1000),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
