<?php

namespace App\Subtitles\VobSub;

use RuntimeException;

class VobSub2SrtFake implements VobSub2SrtInterface
{
    private const OUTPUT_NOTHING = 100;
    private const OUTPUT_SRT = 200;
    private const OUTPUT_SRT_NO_DIALOGUE = 300;
    private const OUTPUT_EMPTY_FILE = 400;
    private const OUTPIT_THROW_EXCEPTION = 500;

    private $output = self::OUTPUT_SRT;

    private $filePathWithoutExtension;

    public function get()
    {
        return $this;
    }

    public function path($pathWithoutExtension)
    {
        $this->filePathWithoutExtension = $pathWithoutExtension;

        if (! file_exists("$this->filePathWithoutExtension.sub")) {
            throw new RuntimeException($this->filePathWithoutExtension.'.sub does not exist');
        }

        if (! file_exists("$this->filePathWithoutExtension.idx")) {
            throw new RuntimeException($this->filePathWithoutExtension.'.idx does not exist');
        }

        return $this;
    }

    public function languages()
    {
        return [
            ['index' => 0, 'language' => 'en'],
            ['index' => 1, 'language' => 'unknown'],
            ['index' => 2, 'language' => 'nl'],
            ['index' => 3, 'language' => 'es'],
        ];
    }

    public function extract($index, $language)
    {
        $outputFilePath = $this->filePathWithoutExtension.'.srt';

        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }

        switch($this->output) {
            case self::OUTPUT_SRT:
                $this->writeSrtFile();
                break;
            case self::OUTPUT_SRT_NO_DIALOGUE:
                $this->writeSrtFileWithoutDialogue();
                break;
            case self::OUTPUT_EMPTY_FILE:
                $this->writeSrtFileWithoutContent();
                break;
            case self::OUTPIT_THROW_EXCEPTION:
                throw new RuntimeException();
            case self::OUTPUT_NOTHING:
                break;
        }

        return $outputFilePath;
    }

    public function outputNothing()
    {
        $this->output = self::OUTPUT_NOTHING;

        return $this;
    }

    public function outputSrt()
    {
        $this->output = self::OUTPUT_SRT;

        return $this;
    }

    public function outputSrtWithNoDialogue()
    {
        $this->output = self::OUTPUT_SRT_NO_DIALOGUE;

        return $this;
    }

    public function outputEmptyFile()
    {
        $this->output = self::OUTPUT_EMPTY_FILE;

        return $this;
    }

    public function outputThrowException()
    {
        $this->output = self::OUTPIT_THROW_EXCEPTION;

        return $this;
    }

    private function writeSrtFileWithoutDialogue()
    {
        $this->writeSrt([
            '1',
            '00:00:01,266 --> 00:00:03,366',
            '',
            '2',
            '00:00:03,400 --> 00:00:06,366',
            '',
            '',
        ]);
    }

    private function writeSrtFile()
    {
        $this->writeSrt([
            '1',
            '00:00:01,266 --> 00:00:03,366',
            'Do you know what this is all',
            'about? Why we\'re here?',
            '',
            '2',
            '00:00:03,400 --> 00:00:06,366',
            'To be out. This is out.',
            '[AUDIENCE LAUGHS]',
            '',
        ]);
    }

    private function writeSrtFileWithoutContent()
    {
        $this->writeSrt(['']);
    }

    private function writeSrt(array $content)
    {
        $data = implode("\r\n", $content);

        file_put_contents("$this->filePathWithoutExtension.srt", $data);
    }
}
