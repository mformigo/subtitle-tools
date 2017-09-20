<?php

Route::get('/')->uses('DashboardController@index')->name('admin');
Route::get('/log/{name}')->uses('DashboardController@getLog')->name('adminGetLog');
Route::get('/log/{name}/delete')->uses('DashboardController@deleteLog')->name('adminDeleteLog');

Route::get('/file-jobs')->uses('FileJobsController@index')->name('adminFileJobs');

Route::get('/stored-file/{id}')->uses('StoredFilesController@detail')->name('adminStoredFileDetail');

Route::post('/stored-file/download')->uses('StoredFilesController@download')->name('adminStoredFileDownload');

Route::fallback(function() {
   return '<pre>404, dummy</pre>';
});
