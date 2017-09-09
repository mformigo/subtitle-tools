<?php

Route::get('st-login',   'Auth\LoginController@showLoginForm')->name('login');
Route::post('st-login',  'Auth\LoginController@login');
Route::post('st-logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/')->uses('HomeController@index')->name('home');
Route::get('/contact')->uses('HomeController@contact')->name('contact');

Route::post('/file-group-archive/{urlKey}')->uses('DownloadController@fileGroupArchive')->name('fileGroupArchiveDownload');

Route::prefix('convert-sub-idx-to-srt-online')->group(function() {
    Route::get('/')->uses('SubIdxController@index')->name('subIdx');
    Route::post('/')->uses('SubIdxController@post');
    Route::get('/{pageId}')->uses('SubIdxController@detail')->name('subIdxDetail');
    Route::post('/{pageId}/{index}')->uses('SubIdxController@downloadSrt')->name('subIdxDownload');
});

Route::fileGroupTool('convertToSrt',  'ConvertToSrtController',  'convert-to-srt-online');
Route::fileGroupTool('cleanSrt',      'CleanSrtController',      'srt-cleaner');
Route::fileGroupTool('shift',         'ShiftController',         'subtitle-sync-shifter');
Route::fileGroupTool('shiftPartial',  'ShiftPartialController',  'partial-subtitle-sync-shifter');
Route::fileGroupTool('convertToUtf8', 'ConvertToUtf8Controller', 'convert-to-utf8');
Route::fileGroupTool('pinyin',        'PinyinController',        'make-chinese-pinyin-subtitles');
Route::fileGroupTool('convertToPlainText', 'ConvertToPlainTextController', 'convert-subtitles-to-plain-text-online');
