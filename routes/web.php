<?php

Route::get('/')->uses('HomeController@index')->name('home');

Route::prefix('convert-sub-idx-to-srt-online')->group(function() {
    Route::get('/')->uses('SubIdxController@index')->name('sub-idx-index');
    Route::post('/')->uses('SubIdxController@post');
    Route::get('/{pageId}')->uses('SubIdxController@detail')->name('sub-idx-detail');
    Route::post('/{pageId}/{index}')->uses('SubIdxController@downloadSrt')->name('sub-idx-dl');
});


Route::prefix('convert-to-srt')->group(function() {
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
