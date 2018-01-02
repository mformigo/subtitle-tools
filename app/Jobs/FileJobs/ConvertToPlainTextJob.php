<?php

namespace App\Jobs\FileJobs;

use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\PlainText;
use App\Subtitles\TransformsToGenericSubtitle;

class ConvertToPlainTextJob extends FileJob
{
    public function handle()
    {
        $this->startFileJob();

        if (!app(\SjorsO\TextFile\Contracts\TextFileIdentifierInterface::class)->isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $inputSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if (!$inputSubtitle instanceof TransformsToGenericSubtitle) {
            return $this->abortFileJob('messages.cant_convert_file_to_plain_text');
        }

        $genericSubtitle = $inputSubtitle->toGenericSubtitle();

        $genericSubtitle->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if (!$genericSubtitle->hasCues()) {
            return $this->abortFileJob('messages.file_has_no_dialogue_to_convert');
        }

        $genericCues = $genericSubtitle->getCues();
        $lines = [];

        foreach ($genericCues as $cue) {
            foreach ($cue->getLines() as $line) {
                $lines[] = $line;
            }

            $lines[] = '';
        }

        $textFile = new PlainText();

        $textFile->setContent(
            implode("\r\n", $lines)
        );

        $outputStoredFile = StoredFile::createFromTextFile($textFile);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return "txt";
    }
}
