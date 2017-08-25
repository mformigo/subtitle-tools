<?php

namespace App\Jobs;

use App\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\Transformers\ChineseToPinyinTransformer;
use App\Subtitles\Transformers\OnlyPinyinAndChineseTransformer;
use App\Subtitles\Transformers\PinyinUnderChineseTransformer;
use App\Subtitles\TransformsToGenericSubtitle;

class PinyinSubtitlesJob extends FileJobJob
{
    public function handle()
    {
        $this->startFileJob();

        if(!app('TextFileIdentifier')->isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $inputSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if(!$inputSubtitle instanceof TransformsToGenericSubtitle && !$inputSubtitle instanceof Srt) {
            return $this->abortFileJob('messages.pinyin.can_not_make_pinyin_subtitles_from_this_file_type');
        }

        $srt = ($inputSubtitle instanceof Srt) ? $inputSubtitle : new Srt($inputSubtitle);

        $srt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if(!$srt->hasCues()) {
            return $this->abortFileJob('messages.file_has_no_dialogue');
        }

        $modeNumber = $this->fileGroup->job_options->mode;

        switch($modeNumber)
        {
            case '1': $transformer = app(ChineseToPinyinTransformer::class); break;
            case '2': $transformer = app(PinyinUnderChineseTransformer::class); break;
            case '3': $transformer = app(OnlyPinyinAndChineseTransformer::class); break;
            default: return $this->abortFileJob('messages.pinyin.unknown_mode');
        }

        $hasChangedSomething = $transformer->transformCues($srt);

        if(!$hasChangedSomething) {
            return $this->abortFileJob('messages.pinyin.subtitles_have_no_chinese');
        }

        $outputStoredFile = StoredFile::createFromTextFile($srt);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return "srt";
    }
}
