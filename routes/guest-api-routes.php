<?php

Route::get('sub-idx/languages/{pageId}', ['uses' => 'SubIdxController@languages', 'as' => 'apiSubIdxLanguages']);

Route::get('file-group/result/{urlKey}',           ['uses' => 'FileGroupController@result',         'as' => 'fileGroupResult']);
Route::get('file-group/archive/{urlKey}',          ['uses' => 'FileGroupController@archive',        'as' => 'fileGroupArchive']);
Route::post('file-group/archive/request/{urlKey}', ['uses' => 'FileGroupController@requestArchive', 'as' => 'fileGroupRequestArchive']);

Route::get('sup-job/{urlKey}', ['uses' => 'SupJobController@show', 'as' => 'api.supJob.show']);
