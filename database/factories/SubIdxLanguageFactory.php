<?php

use App\Models\StoredFile;
use App\Models\SubIdxLanguage;
use Faker\Generator as Faker;

$factory->define(SubIdxLanguage::class, function (Faker $faker) {
    return [
        'index' => (string) mt_rand(0, 12),
        'language' => $faker->randomElement(array_keys(__('languages.subIdx'))),
        'output_stored_file_id' => null,
        'error_message' => null,
        'queued_at' => null,
        'started_at' => null,
        'finished_at' => null,
        'times_downloaded' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->state(SubIdxLanguage::class, 'idle', function (Faker $faker) {
    return [];
});

$factory->state(SubIdxLanguage::class, DETERMINISTIC, function (Faker $faker) {
    static $count = -1;

    $count++;

    return [
        'index' => (string) $count,
        'language' => array_keys(__('languages.subIdx'))[$count],
    ];
});

$factory->state(SubIdxLanguage::class, 'queued', function (Faker $faker) {
    return [
        'queued_at' => now()->addSeconds(5),
    ];
});

$factory->state(SubIdxLanguage::class, 'processing', function (Faker $faker) {
    return [
        'queued_at' => now()->addSeconds(5),
        'started_at' => now()->addSeconds(10),
    ];
});

$factory->state(SubIdxLanguage::class, 'finished', function (Faker $faker) {
    return [
        'output_stored_file_id' => factory(StoredFile::class)->create()->id,
        'queued_at' => now()->addSeconds(5),
        'started_at' => now()->addSeconds(10),
        'finished_at' => now()->addSeconds(15),
        'times_downloaded' => mt_rand(0, 5),
    ];
});

$factory->state(SubIdxLanguage::class, 'failed', function (Faker $faker) {
    return [
        'queued_at' => now()->addSeconds(5),
        'started_at' => now()->addSeconds(10),
        'finished_at' => now()->addSeconds(15),
        'error_message' => 'failed',
    ];
});
