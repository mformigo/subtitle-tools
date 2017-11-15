<?php

Route::get('st-login',   'Auth\LoginController@showLoginForm')->name('login');
Route::post('st-login',  'Auth\LoginController@login');
Route::post('st-logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/')->uses('HomeController@index')->name('home');
Route::get('/contact')->uses('ContactController@index')->name('contact');
Route::post('/contact')->uses('ContactController@post')->name('contact.post');

Route::post('/file-group-archive/{urlKey}')->uses('DownloadController@fileGroupArchive')->name('fileGroupArchiveDownload');
Route::get('/file-group-archive/{urlKey}', function($urlKey) {
    $fileGroup = \App\Models\FileGroup::where('url_key', $urlKey)->firstOrFail();

    return redirect($fileGroup->resultRoute);
});

Route::prefix('convert-sub-idx-to-srt-online')->group(function() {
    Route::get('/',                  ['uses' => 'SubIdxController@index',       'as' => 'subIdx']);
    Route::post('/',                 ['uses' => 'SubIdxController@post']);
    Route::get('/{pageId}',          ['uses' => 'SubIdxController@detail',      'as' => 'subIdx.show']);
    Route::post('/{pageId}/{index}', ['uses' => 'SubIdxController@downloadSrt', 'as' => 'subIdx.download']);

    Route::get('/{pageId}/{index}', function($pageId, $index) {
        return redirect()->route('subIdx.show', $pageId);
    });
});

Route::fileGroupTool('convertToSrt',       'ConvertToSrtController',       'convert-to-srt-online');
Route::fileGroupTool('cleanSrt',           'CleanSrtController',           'srt-cleaner');
Route::fileGroupTool('shift',              'ShiftController',              'subtitle-sync-shifter');
Route::fileGroupTool('shiftPartial',       'ShiftPartialController',       'partial-subtitle-sync-shifter');
Route::fileGroupTool('convertToUtf8',      'ConvertToUtf8Controller',      'convert-text-files-to-utf8-online');
Route::fileGroupTool('pinyin',             'PinyinController',             'make-chinese-pinyin-subtitles');
Route::fileGroupTool('convertToPlainText', 'ConvertToPlainTextController', 'convert-subtitles-to-plain-text-online');

Route::get('/how-to-fix-vlc-subtitles-displaying-as-boxes', ['uses' => 'HomeController@blogVlcSubtitleBoxes', 'as' => 'blogVlcSubtitleBoxes']);


Route::prefix('convert-sup-to-srt-online')->group(function() {
    Route::get('/',                   ['uses' => 'SupController@index',    'as' => 'sup']);
    Route::post('/',                  ['uses' => 'SupController@post',     'as' => 'sup.post']);
    Route::get('/{urlKey}',           ['uses' => 'SupController@show',     'as' => 'sup.show']);
    Route::post('/{urlKey}/download', ['uses' => 'SupController@download', 'as' => 'sup.show.download']);

    Route::get('/{urlKey}/download', function($urlKey) {
        return redirect()->route('sup.show', $urlKey);
    });
});


Route::fallback('NotFoundController@index');
