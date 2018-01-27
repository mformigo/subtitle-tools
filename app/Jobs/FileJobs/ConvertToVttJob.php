<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\PlainText\WebVtt;
use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\TransformsToGenericSubtitle;
use SjorsO\TextFile\Facades\TextFileIdentifier;

class ConvertToVttJob extends FileJob
{
    public function handle()
    {
        $this->startFileJob();

        if (! TextFileIdentifier::isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $inputSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if (! $inputSubtitle instanceof TransformsToGenericSubtitle) {
            return $this->abortFileJob('messages.cant_convert_file_to_vtt');
        }

        $vtt = ($inputSubtitle instanceof WebVtt)
            ? $inputSubtitle
            : new WebVtt($inputSubtitle);

        $vtt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if (! $vtt->hasCues()) {
            return $this->abortFileJob('messages.file_has_no_dialogue_to_convert');
        }

        $outputStoredFile = StoredFile::createFromTextFile($vtt);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return 'vtt';
    }
}
