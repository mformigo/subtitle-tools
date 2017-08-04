<?php

Route::get('/')->uses('HomeController@index')->name('home');

Route::get('/convert-sub-idx-to-srt-online')->uses('SubIdxController@index')->name('sub-idx-index');
Route::post('/convert-sub-idx-to-srt-online')->uses('SubIdxController@post');
Route::get('/convert-sub-idx-to-srt-online/{pageId}')->uses('SubIdxController@detail')->name('sub-idx-detail');
