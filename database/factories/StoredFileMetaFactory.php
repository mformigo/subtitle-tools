<?php

use App\Models\StoredFileMeta;
use Faker\Generator as Faker;

$factory->define(StoredFileMeta::class, function (Faker $faker) {
    return [
        'size' => mt_rand(100, 10000000),
        'mime' => $mime = $faker->randomElement([
            'text/plain',
            'application/zip',
            'text/xml',
            'video/mpeg',
        ]),

        'is_text_file' => $isTextFile = strpos('text/', $mime) === 0,

        'encoding' => $isTextFile ? $faker->randomElement([
            'UTF-8',
            'windows-1252',
            'windows-1250',
            'ISO-8859-7',
            'EUC-KR',
        ]) : null,

        'identified_as' => $isTextFile ? $faker->randomElement([
            'App\Subtitles\PlainText\Smi',
            'App\Subtitles\PlainText\PlainText',
            'App\Subtitles\PlainText\Srt',
            'App\Subtitles\PlainText\Ass',
        ]) : null,

        'line_endings' => $isTextFile ? $faker->randomElement(['CRLF', 'LF']) : null,
        'line_count' => $isTextFile ? mt_rand(1, 10000) : null,
        'language' => $isTextFile ? $faker->randomElement([array_keys(config('languages.subIdx'))]) : null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
