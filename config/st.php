<?php

return [

    /**
     * These tool routes are use for FileJobStats.
     */
    'tool_routes' => [
        'convertToSrt',
        'convertToVtt',
        'cleanSrt',
        'shift',
        'shiftPartial',
        'convertToUtf8',
        'pinyin',
        'convertToPlainText',
        'merge',
    ],

    /**
     * Checked migration is used in:
     *   PruneStoredFiles command
     *
     */
    'checked-migration' => '2018_10_27_150839_add_last_cache_hit_column_to_subidx_table',

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


