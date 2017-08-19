<?php

Route::prefix('api/v1/')->group(function() {

    Route::get('sub-idx/languages/{pageId}')->uses('SubIdxController@languages')->name('api-sub-idx-languages');

    Route::get('file-group/result/{urlKey}')->uses('FileGroupController@result')->name('file-group-result');
    Route::get('file-group/archive/{urlKey}')->uses('FileGroupController@archive')->name('file-group-archive');
    Route::post('file-group/archive/request/{urlKey}')->uses('FileGroupController@requestArchive')->name('file-group-request-archive');

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
