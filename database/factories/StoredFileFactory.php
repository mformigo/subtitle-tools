<?php

use App\Models\StoredFile;
use Faker\Generator as Faker;

$factory->define(StoredFile::class, function (Faker $faker) {
    $now = now();

    return [
        'hash' => $hash = sha1(str_random(40)),
        'storage_file_path' => 'stored-files/'.$now->format('Y-W').'/'.$now->format('z').'/'.$now->format('U').'-'.substr($hash, 0 ,16),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
