<?php

Route::get('/')->uses('DashboardController@index')->name('admin');
Route::get('/log/{name}')->uses('DashboardController@getLog')->name('adminGetLog');
Route::get('/log/{name}/delete')->uses('DashboardController@deleteLog')->name('adminDeleteLog');

Route::get('/file-jobs')->uses('FileJobsController@index')->name('adminFileJobs');

Route::get('/stored-file/{id}')->uses('StoredFilesController@detail')->name('adminStoredFileDetail');

Route::post('/stored-file/download')->uses('StoredFilesController@download')->name('adminStoredFileDownload');


Route::post('/dashboard/404/open',      ['uses' => 'DashboardController@open404Log',   'as' => 'admin.dashboard.open404Log']);
Route::post('/dashboard/404/delete',    ['uses' => 'DashboardController@delete404Log', 'as' => 'admin.dashboard.delete404Log']);
Route::post('/dashboard/404/blacklist', ['uses' => 'DashboardController@append404Blacklist', 'as' => 'admin.dashboard.append404Blacklist']);


Route::fallback(function() {
   return '<pre>404, dummy</pre>';
});
