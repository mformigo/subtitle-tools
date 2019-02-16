<?php

use Faker\Generator as Faker;

$factory->define(App\Models\DiskUsage::class, function (Faker $faker) {
    $gb2b = function ($int) {
        return $int * 1024 * 1024 * 1024;
    };

    return [
        'total_size' => $totalSize = $gb2b(156),
        'total_used' => $gb2b($faker->numberBetween(1, 10)),
        'stored_files_dir_size' => $gb2b($faker->numberBetween(1, 10)),
        'sub_idx_dir_size' => $gb2b($faker->numberBetween(1, 10)),
        'temp_dirs_dir_size' => $gb2b($faker->numberBetween(1, 10)),
        'temp_files_dir_size' => $gb2b($faker->numberBetween(1, 10)),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
