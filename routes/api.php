<?php

use Illuminate\Http\Request;

Route::prefix('api/v1/')->group(function() {

    Route::get('sub-idx/languages/{pageId}')->uses('SubIdxController@languages')->name('api-sub-idx-languages');

    Route::get('file-group/result/{pageId}')->uses('FileGroupController@result')->name('file-group-result');

});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
