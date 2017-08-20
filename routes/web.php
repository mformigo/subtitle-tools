<?php

Route::get('/')->uses('HomeController@index')->name('home');

Route::post('/file-group-archive/{urlKey}')->uses('DownloadController@fileGroupArchive')->name('file-group-archive-download');

Route::prefix('convert-sub-idx-to-srt-online')->group(function() {
    Route::get('/')->uses('SubIdxController@index')->name('sub-idx-index');
    Route::post('/')->uses('SubIdxController@post');
    Route::get('/{pageId}')->uses('SubIdxController@detail')->name('sub-idx-detail');
    Route::post('/{pageId}/{index}')->uses('SubIdxController@downloadSrt')->name('sub-idx-download');
});


Route::prefix('convert-to-srt-online')->group(function() {
    Route::get('/')->uses('ConvertToSrtController@index')->name('convert-to-srt');
    Route::post('/')->uses('ConvertToSrtController@post');
    Route::get('/{urlKey}')->uses('ConvertToSrtController@result')->name('convert-to-srt-result');
    Route::get('/{urlKey}/{id}')->uses('ConvertToSrtController@download')->name('convert-to-srt-download');
});


Route::prefix('srt-cleaner')->group(function() {
    Route::get('/')->uses('CleanSrtController@index')->name('clean-srt');
    Route::post('/')->uses('CleanSrtController@post');
    Route::get('/{urlKey}')->uses('CleanSrtController@result')->name('clean-srt-result');
    Route::get('/{urlKey}/{id}')->uses('CleanSrtController@download')->name('clean-srt-download');
});


Route::prefix('subtitle-sync-shifter')->group(function() {
    Route::get('/')->uses('ShiftController@index')->name('shift');
    Route::post('/')->uses('ShiftController@post');
    Route::get('/{urlKey}')->uses('ShiftController@result')->name('shift-result');
    Route::get('/{urlKey}/{id}')->uses('ShiftController@download')->name('shift-download');
});
