<?php

Route::get('/')->uses('HomeController@index')->name('home');

Route::prefix('convert-sub-idx-to-srt-online')->group(function() {
    Route::get('/')->uses('SubIdxController@index')->name('sub-idx-index');
    Route::post('/')->uses('SubIdxController@post');
    Route::get('/{pageId}')->uses('SubIdxController@detail')->name('sub-idx-detail');
    Route::post('/{pageId}/{index}')->uses('SubIdxController@downloadSrt')->name('sub-idx-dl');
});
