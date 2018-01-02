<?php

namespace App\Subtitles\VobSub;

use LogicException;
use RuntimeException;

class VobSub2Srt implements VobSub2SrtInterface
{
    protected $filePathWithoutExtension;

    protected $idxFile;

    public function __construct($pathWithoutExtension)
    {
        $this->filePathWithoutExtension = $pathWithoutExtension;

        if (! file_exists("{$this->filePathWithoutExtension}.sub") || ! file_exists("{$this->filePathWithoutExtension}.idx")) {
            throw new RuntimeException($this->filePathWithoutExtension.'.sub/.idx does not exist');
        }

        $this->idxFile = new IdxFile($this->filePathWithoutExtension.'.idx');
    }

    public function getLanguages()
    {
        $outputLines = $this->execVobsub2srt('--langlist');

        if (! in_array('Languages:', $outputLines)) {
            return [];
        }

        $languages = [];

        foreach ($outputLines as $line) {
            if (preg_match('/^(?<index>\d+): ([a-z]+|\(no id\))$/', $line, $match)) {
                $languages[] = [
                    'index'    => $match['index'],
                    'language' => $this->idxFile->getLanguageForIndex($match['index']),
                ];
            }
        }

        return $languages;
    }

    public function extractLanguage($index, $language)
    {
        $outputFilePath = $this->filePathWithoutExtension.'.srt';

        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }

        $extra = ($language === 'zh') ? ' --tesseract-lang chi_sim' : '';

        $this->execVobsub2srt('--index '.$index.$extra);

        // The vobsub2srt output file does not always exist
        return $outputFilePath;
    }

    protected function execVobsub2srt($argument)
    {
        if (empty($argument)) {
            throw new LogicException('Argument can not be empty');
        }

        $output = shell_exec("timeout 300 /usr/local/bin/vobsub2srt \"{$this->filePathWithoutExtension}\" {$argument} 2>&1");

        return preg_split("/\r\n|\n|\r/", trim($output));
    }

}
