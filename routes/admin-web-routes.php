<?php

Route::get('/', ['uses' => 'DashboardController@index', 'as' => 'dashboard.index']);

Route::delete('/error-log', ['uses' => 'ErrorLogController@delete', 'as' => 'errorLog.delete']);

Route::delete('/feedback', ['uses' => 'FeedbackController@delete', 'as' => 'feedback.delete']);

Route::delete('/failed-jobs/truncate', ['uses' => 'FailedJobsController@truncate', 'as' => 'failedJobs.truncate']);

Route::get('/sup',           ['uses' => 'SupController@index', 'as' => 'sup.index']);
Route::post('/sup/{supJob}', ['uses' => 'SupController@retry', 'as' => 'sup.retry']);

Route::get('/disk-usage', ['uses' => 'DiskUsageController@index', 'as' => 'diskUsage.index']);

Route::get('/debug-tools', ['uses' => 'ToolsController@index', 'as' => 'tools.index']);

Route::get('/stored-file/{storedFile}', ['uses' => 'StoredFilesController@show',     'as' => 'storedFiles.show']);
Route::post('/stored-file/download',    ['uses' => 'StoredFilesController@download', 'as' => 'storedFiles.delete']);
Route::delete('/stored-file/delete',    ['uses' => 'StoredFilesController@delete',   'as' => 'storedFiles.delete']);

Route::get('/file-jobs', ['uses' => 'FileJobsController@index', 'as' => 'fileJobs.index']);

Route::get('/sub-idx', ['uses' => 'SubIdxController@index', 'as' => 'subIdx.index']);

Route::post('/convert-to-utf8')->uses('ConvertToUtf8')->name('convertToUtf8');
Route::get('/phpinfo')->uses('ShowPhpInfo')->name('showPhpinfo');
