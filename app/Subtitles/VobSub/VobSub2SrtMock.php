<?php

namespace App\Subtitles\VobSub;

use App\Models\SubIdx;

class VobSub2SrtMock implements VobSub2SrtInterface
{
    private $logToSubIdx = null;
    private $filePathWithoutExtension;

    public function __construct($pathWithoutExtension, $logToSubIdx = null)
    {
        $this->filePathWithoutExtension = $pathWithoutExtension;

        if($logToSubIdx !== null) {
            $this->logToSubIdx = ($logToSubIdx instanceof SubIdx) ? $logToSubIdx : SubIdx::findOrFail($logToSubIdx);
        }

        if(!file_exists("{$this->filePathWithoutExtension}.sub") || !file_exists("{$this->filePathWithoutExtension}.idx")) {
            throw new \Exception("{$this->filePathWithoutExtension}.sub/.idx does not exist");
        }
    }

    public function getLanguages()
    {
        if($this->logToSubIdx !== null) {
            $this->logToSubIdx->vobsub2srtOutputs()->create([
                'argument' => '--langlist',
                'index'    => null,
                'output'   => 'mock mock mock mock mock mock mock mock mock mock mock mock mock mock',
            ]);
        }

        return [
            ['index' => 0, 'language' => 'en'],
            ['index' => 1, 'language' => 'unknown'],
            ['index' => 2, 'language' => 'nl'],
            ['index' => 3, 'language' => 'es'],
        ];
    }

    public function extractLanguage($index, $language)
    {
        // sleep(2);

        $outputFilePath = "{$this->filePathWithoutExtension}.srt";

        if(file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }

        switch($index) {
            case 0:
                $this->writeSrtFile();
                break;
            case 1:
                $this->writeSrtFileWithoutDialogue();
                break;
            case 2:
                $this->writeSrtFileWithoutContent();
                break;
            case 3:
                // don't write any output file
                break;
        }

        if($this->logToSubIdx !== null) {
            $this->logToSubIdx->vobsub2srtOutputs()->create([
                'argument' => "--index {$index}",
                'index'    => $index,
                'output'   => 'mock mock mock mock mock mock mock mock mock mock mock mock mock mock',
            ]);
        }

        return $outputFilePath;
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
        $filePath = "{$this->filePathWithoutExtension}.srt";

        $data = implode("\r\n", $content);

        file_put_contents($filePath, $data);
    }

}
