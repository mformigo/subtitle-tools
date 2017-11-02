<?php

Route::prefix('api/v1/')->group(function() {

    Route::get('sub-idx/languages/{pageId}')->uses('SubIdxController@languages')->name('apiSubIdxLanguages');

    Route::get('file-group/result/{urlKey}')->uses('FileGroupController@result')->name('fileGroupResult');
    Route::get('file-group/archive/{urlKey}')->uses('FileGroupController@archive')->name('fileGroupArchive');
    Route::post('file-group/archive/request/{urlKey}')->uses('FileGroupController@requestArchive')->name('fileGroupRequestArchive');

    Route::get('sup-job/{urlKey}')->uses('SupJobController@show')->name('api.supJob.show');
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
