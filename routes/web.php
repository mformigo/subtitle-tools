<?php

Route::get('/')->uses('HomeController@index')->name('home');

Route::post('/file-group-archive/{urlKey}')->uses('DownloadController@fileGroupArchive')->name('file-group-archive-download');

Route::prefix('convert-sub-idx-to-srt-online')->group(function() {
    Route::get('/')->uses('SubIdxController@index')->name('sub-idx-index');
    Route::post('/')->uses('SubIdxController@post');
    Route::get('/{pageId}')->uses('SubIdxController@detail')->name('sub-idx-detail');
    Route::post('/{pageId}/{index}')->uses('SubIdxController@downloadSrt')->name('sub-idx-download');
});

Route::fileGroupTool('convert-to-srt',  'ConvertToSrtController',  'convert-to-srt-online');
Route::fileGroupTool('clean-srt',       'CleanSrtController',      'srt-cleaner');
Route::fileGroupTool('shift',           'ShiftController',         'subtitle-sync-shifter');
Route::fileGroupTool('shift-partial',   'ShiftPartialController',  'partial-subtitle-sync-shifter');
Route::fileGroupTool('convert-to-utf8', 'ConvertToUtf8Controller', 'convert-to-utf8');
Route::fileGroupTool('pinyin',          'PinyinController',        'make-chinese-pinyin-subtitles');
