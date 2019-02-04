<?php

use App\Http\Controllers\ConvertToSrtController;
use App\Http\Controllers\ConvertToVttController;
use App\Http\Controllers\CleanSrtController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftPartialController;
use App\Http\Controllers\ConvertToUtf8Controller;
use App\Http\Controllers\PinyinController;
use App\Http\Controllers\ConvertToPlainTextController;
use App\Http\Controllers\MergeController;

return [

    /**
     * These tool routes are use for FileJobStats.
     */
    'tool_routes' => [
        ConvertToSrtController::class => 'convertToSrt',
        ConvertToVttController::class => 'convertToVtt',
        CleanSrtController::class => 'cleanSrt',
        ShiftController::class => 'shift',
        ShiftPartialController::class => 'shiftPartial',
        ConvertToUtf8Controller::class => 'convertToUtf8',
        PinyinController::class => 'pinyin',
        ConvertToPlainTextController::class => 'convertToPlainText',
        MergeController::class => 'merge',
    ],

    /**
     * Checked migration is used in:
     *   PruneStoredFiles command
     *
     */
    'checked-migration' => '2019_01_26_104911_drop_sub_idx_meta_table',

    'tesseract' => [

        // these languages have a .traineddata file
        'languages' => [
            'eng', // default language, should be on top
//            'afr',
//            'amh',
            'ara',
//            'asm',
//            'aze',
            'bel',
//            'ben',
//            'bod',
//            'bos',
            'bul',
//            'cat',
//            'ceb',
//            'ces',
            'chinese', // chi_sim + chi_tra
//            'chr',
//            'cym',
            'dan',
            'deu',
//            'dzo',
            'ell',

//            'enm',
            'epo',
//            'equ',
            'est',
//            'eus',
            'fas',
            'fin',
            'fra',
//            'frk',
//            'frm',
            'gle',
//            'glg',
//            'grc',
//            'guj',
//            'hat',
            'heb',
            'hin',
            'hrv',
            'hun',
//            'iku',
            'ind',
//            'isl',
            'ita',
//            'jav',
            'jpn',
//            'kan',
//            'kat',
//            'kaz',
//            'khm',
//            'kir',
            'kor',
//            'kur',
//            'lao',
//            'lat',
            'lav',
            'lit',
            'mal',
//            'mar',
//            'mkd',
//            'mlt',
//            'msa',
//            'mya',
//            'nep',
            'nld',
            'nor',
//            'ori',
//            'osd',
//            'pan',
            'pol',
            'por',
//            'pus',
//            'ron',
            'rus',
//            'san',
//            'sin', // Sri Lanka
//            'slk',
            'slv',
            'spa',
//            'sqi',
            'srp',
//            'swa', // Swahili
            'swe',
//            'syr', // Syriac
//            'tam', // Tamil
//            'tel', // Telugu
//            'tgk', // Tajik
//            'tgl',
            'tha',
//            'tir',
            'tur',
//            'uig',
            'ukr',
//            'urd',
//            'uzb',
            'vie',
            'yid',
        ],
    ],

];


