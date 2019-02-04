<?php

Route::redirect('/format-converter', route('convertToSrt'), 301);
Route::redirect('/convert-to-srt', route('convertToSrt'), 301);
Route::redirect('/fo...', route('convertToSrt'), 301);
Route::redirect('/convert-to-srt-on...', route('convertToSrt'), 301);
Route::redirect('/c...', route('convertToSrt'), 301);
Route::redirect('/tools', '/', 301);
Route::redirect('/chinese-to-pinyin', route('pinyin'), 301);
Route::redirect('/subtitle-shift', route('shift'), 301);
Route::redirect('/partial-subtitle-shifter', route('shiftPartial'), 301);
Route::redirect('/multi-subtitle-shift', route('shiftPartial'), 301);
Route::redirect('/convert-to-utf8', route('convertToUtf8'), 301);
Route::redirect('/convert-sub-idx-to-srt', route('subIdx'), 301);
