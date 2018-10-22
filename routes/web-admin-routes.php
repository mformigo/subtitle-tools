<?php

Route::get('/')->uses('DashboardController@index')->name('admin');
Route::get('/log/{name}')->uses('DashboardController@getLog')->name('adminGetLog');
Route::get('/log/{name}/delete')->uses('DashboardController@deleteLog')->name('adminDeleteLog');

Route::get('/phpinfo')->uses('ShowPhpInfo')->name('admin.dashboard.phpinfo');

Route::get('/file-jobs')->uses('FileJobsController@index')->name('adminFileJobs');
Route::get('/sub-idx')->uses('SubIdxController@index')->name('admin.subIdx');

Route::get('/failed-jobs')->uses('FailedJobsController@index')->name('admin.failedJobs');
Route::get('/failed-jobs/truncate')->uses('FailedJobsController@truncate')->name('admin.failedJobs.truncate');

Route::get('/sup',           ['uses' => 'SupController@index', 'as' => 'admin.sup']);
Route::post('/sup/{supJob}', ['uses' => 'SupController@retry', 'as' => 'admin.sup.retry']);



Route::get('/stored-file/{id}')->uses('StoredFilesController@detail')->name('adminStoredFileDetail');

Route::post('/stored-file/download')->uses('StoredFilesController@download')->name('adminStoredFileDownload');

Route::post('/convert-to-utf8')->uses('ConvertToUtf8')->name('admin.ConvertToUtf8');

Route::fallback(function () {
   return '<pre>404, dummy</pre>';
});
