<?php

Route::get('sub-idx/{urlKey}/languages',               ['uses' => 'SubIdxController@languages',       'as' => 'subIdx.languages']);
Route::post('sub-idx/{urlKey}/languages/{languageId}', ['uses' => 'SubIdxController@extractLanguage', 'as' => 'subIdx.post']);

Route::get('file-group/result/{urlKey}',           ['uses' => 'FileGroupController@result',         'as' => 'fileGroup.result']);
Route::get('file-group/archive/{urlKey}',          ['uses' => 'FileGroupController@archive',        'as' => 'fileGroup.archive']);
Route::post('file-group/archive/request/{urlKey}', ['uses' => 'FileGroupController@requestArchive', 'as' => 'fileGroup.requestArchive']);

Route::get('sup-job/{urlKey}', ['uses' => 'SupJobController@show', 'as' => 'supJob.show']);
