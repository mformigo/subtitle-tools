<?php

Route::get('/')->uses('DashboardController@index')->name('admin');
Route::get('/log/{name}')->uses('DashboardController@getLog')->name('adminGetLog');
Route::get('/log/{name}/delete')->uses('DashboardController@deleteLog')->name('adminDeleteLog');
