<?php

namespace App\Subtitles\Tools;

use App\Subtitles\PlainText\PlainText;
use App\Subtitles\Tools\Options\ToPlainTextOptions;
use App\Subtitles\TransformsToGenericSubtitle;
use LogicException;

class ToPlainText
{
    public $error;

    private $options;

    public function __construct()
    {
        $this->options = new ToPlainTextOptions();
    }

    public function options(ToPlainTextOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    public function convert(TransformsToGenericSubtitle $input): ?PlainText
    {
        $genericSubtitle = $input->toGenericSubtitle()
            ->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if (! $genericSubtitle->hasCues()) {
            return $this->failed('messages.file_has_no_dialogue_to_convert');
        }

        $lines = [];

        foreach ($genericSubtitle->getCues() as $cue) {
            foreach ($cue->getLines() as $line) {
                $lines[] = $line;
            }

            if ($this->options->newLineBetweenCues) {
                $lines[] = '';
            }
        }

        $plainText = new PlainText();

        $plainText->setContent(
            implode("\r\n", $lines)
        );

        return $plainText;
    }

    private function failed($message)
    {
        $this->error = $message;

        if (! $this->hasError()) {
            throw new LogicException();
        }

        return null;
    }

    public function hasError()
    {
        return (bool) $this->error;
    }
}
