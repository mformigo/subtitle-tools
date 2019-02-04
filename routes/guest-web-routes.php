<?php

Route::get('st-login',   ['uses' => 'LoginController@index',  'as' => 'login'])->middleware('guest');
Route::post('st-login',  ['uses' => 'LoginController@login',  'as' => 'login.post']);
Route::post('st-logout', ['uses' => 'LoginController@logout', 'as' => 'logout']);

Route::view('/', 'home')->name('home');

Route::view('/how-to-fix-vlc-subtitles-displaying-as-boxes', 'blogs.fix-vlc-subtitle-boxes')->name('blog.vlcSubtitleBoxes');

Route::get('/contact',  ['uses' => 'ContactController@index', 'as' => 'contact']);
Route::post('/contact', ['uses' => 'ContactController@post',  'as' => 'contact.post']);

Route::get('/stats', ['uses' => 'StatsController@index', 'as' => 'stats']);

Route::post('/file-group-archive/{urlKey}', ['uses' => 'FileGroupArchiveController@download',         'as' => 'fileGroup.archive.download']);
Route::get('/file-group-archive/{urlKey}',  ['uses' => 'FileGroupArchiveController@downloadRedirect', 'as' => 'fileGroup.archive.downloadRedirect']);


Route::get('convert-sub-idx-to-srt-online',                   ['uses' => 'SubIdxController@index',            'as' => 'subIdx']);
Route::post('convert-sub-idx-to-srt-online',                  ['uses' => 'SubIdxController@post',             'as' => 'subIdx.post'])->middleware('swap-sub-and-idx');
Route::get('convert-sub-idx-to-srt-online/{urlKey}',          ['uses' => 'SubIdxController@show',             'as' => 'subIdx.show']);
Route::post('convert-sub-idx-to-srt-online/{urlKey}/{index}', ['uses' => 'SubIdxController@downloadSrt',      'as' => 'subIdx.download']);
Route::get('convert-sub-idx-to-srt-online/{urlKey}/{index}',  ['uses' => 'SubIdxController@downloadRedirect', 'as' => 'subIdx.downloadRedirect']);


Route::get('/merge-subtitles-online/',               ['uses' => 'MergeController@index',            'as' => 'merge']);
Route::post('/merge-subtitles-online/',              ['uses' => 'MergeController@post',             'as' => 'merge.post']);
Route::get('/merge-subtitles-online/{urlKey}',       ['uses' => 'MergeController@result',           'as' => 'merge.result']);
Route::post('/merge-subtitles-online/{urlKey}/{id}', ['uses' => 'MergeController@download',         'as' => 'merge.download']);
Route::get('/merge-subtitles-online/{urlKey}/{id}',  ['uses' => 'MergeController@downloadRedirect', 'as' => 'merge.downloadRedirect']);


Route::get('/convert-subtitles-to-plain-text-online/',               ['uses' => 'ConvertToPlainTextController@index',            'as' => 'convertToPlainText']);
Route::post('/convert-subtitles-to-plain-text-online/',              ['uses' => 'ConvertToPlainTextController@post',             'as' => 'convertToPlainText.post']);
Route::get('/convert-subtitles-to-plain-text-online/{urlKey}',       ['uses' => 'ConvertToPlainTextController@result',           'as' => 'convertToPlainText.result']);
Route::post('/convert-subtitles-to-plain-text-online/{urlKey}/{id}', ['uses' => 'ConvertToPlainTextController@download',         'as' => 'convertToPlainText.download']);
Route::get('/convert-subtitles-to-plain-text-online/{urlKey}/{id}',  ['uses' => 'ConvertToPlainTextController@downloadRedirect', 'as' => 'convertToPlainText.downloadRedirect']);


Route::get('/convert-text-files-to-utf8-online/',               ['uses' => 'ConvertToUtf8Controller@index',            'as' => 'convertToUtf8']);
Route::post('/convert-text-files-to-utf8-online/',              ['uses' => 'ConvertToUtf8Controller@post',             'as' => 'convertToUtf8.post']);
Route::get('/convert-text-files-to-utf8-online/{urlKey}',       ['uses' => 'ConvertToUtf8Controller@result',           'as' => 'convertToUtf8.result']);
Route::post('/convert-text-files-to-utf8-online/{urlKey}/{id}', ['uses' => 'ConvertToUtf8Controller@download',         'as' => 'convertToUtf8.download']);
Route::get('/convert-text-files-to-utf8-online/{urlKey}/{id}',  ['uses' => 'ConvertToUtf8Controller@downloadRedirect', 'as' => 'convertToUtf8.downloadRedirect']);


Route::get('/partial-subtitle-sync-shifter/',               ['uses' => 'ShiftPartialController@index',            'as' => 'shiftPartial']);
Route::post('/partial-subtitle-sync-shifter/',              ['uses' => 'ShiftPartialController@post',             'as' => 'shiftPartial.post']);
Route::get('/partial-subtitle-sync-shifter/{urlKey}',       ['uses' => 'ShiftPartialController@result',           'as' => 'shiftPartial.result']);
Route::post('/partial-subtitle-sync-shifter/{urlKey}/{id}', ['uses' => 'ShiftPartialController@download',         'as' => 'shiftPartial.download']);
Route::get('/partial-subtitle-sync-shifter/{urlKey}/{id}',  ['uses' => 'ShiftPartialController@downloadRedirect', 'as' => 'shiftPartial.downloadRedirect']);


Route::get('/subtitle-sync-shifter/',               ['uses' => 'ShiftController@index',            'as' => 'shift']);
Route::post('/subtitle-sync-shifter/',              ['uses' => 'ShiftController@post',             'as' => 'shift.post']);
Route::get('/subtitle-sync-shifter/{urlKey}',       ['uses' => 'ShiftController@result',           'as' => 'shift.result']);
Route::post('/subtitle-sync-shifter/{urlKey}/{id}', ['uses' => 'ShiftController@download',         'as' => 'shift.download']);
Route::get('/subtitle-sync-shifter/{urlKey}/{id}',  ['uses' => 'ShiftController@downloadRedirect', 'as' => 'shift.downloadRedirect']);


Route::get('/srt-cleaner/',               ['uses' => 'CleanSrtController@index',            'as' => 'cleanSrt']);
Route::post('/srt-cleaner/',              ['uses' => 'CleanSrtController@post',             'as' => 'cleanSrt.post']);
Route::get('/srt-cleaner/{urlKey}',       ['uses' => 'CleanSrtController@result',           'as' => 'cleanSrt.result']);
Route::post('/srt-cleaner/{urlKey}/{id}', ['uses' => 'CleanSrtController@download',         'as' => 'cleanSrt.download']);
Route::get('/srt-cleaner/{urlKey}/{id}',  ['uses' => 'CleanSrtController@downloadRedirect', 'as' => 'cleanSrt.downloadRedirect']);


Route::get('/convert-to-vtt-online/',               ['uses' => 'ConvertToVttController@index',            'as' => 'convertToVtt']);
Route::post('/convert-to-vtt-online/',              ['uses' => 'ConvertToVttController@post',             'as' => 'convertToVtt.post']);
Route::get('/convert-to-vtt-online/{urlKey}',       ['uses' => 'ConvertToVttController@result',           'as' => 'convertToVtt.result']);
Route::post('/convert-to-vtt-online/{urlKey}/{id}', ['uses' => 'ConvertToVttController@download',         'as' => 'convertToVtt.download']);
Route::get('/convert-to-vtt-online/{urlKey}/{id}',  ['uses' => 'ConvertToVttController@downloadRedirect', 'as' => 'convertToVtt.downloadRedirect']);


Route::get('/convert-to-srt-online/',               ['uses' => 'ConvertToSrtController@index',            'as' => 'convertToSrt']);
Route::post('/convert-to-srt-online/',              ['uses' => 'ConvertToSrtController@post',             'as' => 'convertToSrt.post']);
Route::get('/convert-to-srt-online/{urlKey}',       ['uses' => 'ConvertToSrtController@result',           'as' => 'convertToSrt.result']);
Route::post('/convert-to-srt-online/{urlKey}/{id}', ['uses' => 'ConvertToSrtController@download',         'as' => 'convertToSrt.download']);
Route::get('/convert-to-srt-online/{urlKey}/{id}',  ['uses' => 'ConvertToSrtController@downloadRedirect', 'as' => 'convertToSrt.downloadRedirect']);


Route::get('/make-chinese-pinyin-subtitles/',               ['uses' => 'PinyinController@index',            'as' => 'pinyin']);
Route::post('/make-chinese-pinyin-subtitles/',              ['uses' => 'PinyinController@post',             'as' => 'pinyin.post']);
Route::get('/make-chinese-pinyin-subtitles/{urlKey}',       ['uses' => 'PinyinController@result',           'as' => 'pinyin.result']);
Route::post('/make-chinese-pinyin-subtitles/{urlKey}/{id}', ['uses' => 'PinyinController@download',         'as' => 'pinyin.download']);
Route::get('/make-chinese-pinyin-subtitles/{urlKey}/{id}',  ['uses' => 'PinyinController@downloadRedirect', 'as' => 'pinyin.downloadRedirect']);


Route::get('/convert-sup-to-srt-online',                    ['uses' => 'SupController@index',            'as' => 'sup']);
Route::post('/convert-sup-to-srt-online',                   ['uses' => 'SupController@post',             'as' => 'sup.post']);
Route::get('/convert-sup-to-srt-online/{urlKey}',           ['uses' => 'SupController@show',             'as' => 'sup.show']);
Route::post('/convert-sup-to-srt-online/{urlKey}/download', ['uses' => 'SupController@download',         'as' => 'sup.show.download']);
Route::get('/convert-sup-to-srt-online/{urlKey}/download',  ['uses' => 'SupController@downloadRedirect', 'as' => 'sup.show.downloadRedirect']);
