<?php

use App\Models\SubIdx;
use Faker\Generator as Faker;

$factory->define(SubIdx::class, function (Faker $faker) {
    return [
        'url_key' => generate_url_key(),
        'filename' => $fileName = strtolower(str_random(12)),
        'store_directory' => 'sub-idx/'.now()->format('Y-z/U').'-'.$fileName.'/',
        'original_name' => snake_case($faker->words(3, true)),
        'sub_hash' => sha1(str_random(40)),
        'idx_hash' => sha1(str_random(40)),
        'is_readable' => true,
        'last_cache_hit' => null,
        'cache_hits' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
