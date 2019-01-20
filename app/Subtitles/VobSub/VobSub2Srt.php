<?php

namespace App\Subtitles\VobSub;

use RuntimeException;

class VobSub2Srt implements VobSub2SrtInterface
{
    private $filePathWithoutExtension;

    /** @var IdxFile $idxFile */
    private $idxFile;

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

        $this->idxFile = new IdxFile("$this->filePathWithoutExtension.idx");

        return $this;
    }

    public function languages()
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

    public function extract($index, $language)
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

    private function execVobsub2srt($argument)
    {
        if (! $argument) {
            throw new RuntimeException('Argument can not be empty');
        }

        $output = shell_exec("timeout 300 /usr/local/bin/vobsub2srt \"{$this->filePathWithoutExtension}\" {$argument} 2>&1");

        return preg_split("/\r\n|\n|\r/", trim($output));
    }
}
