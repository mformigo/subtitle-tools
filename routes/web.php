<?php

Route::get('/')->uses('HomeController@index')->name('home');

Route::get('/convert-sub-idx-to-srt-online')->uses('SubIdxController@index')->name('sub-idx-page');
Route::post('/convert-sub-idx-to-srt-online')->uses('SubIdxController@post');
