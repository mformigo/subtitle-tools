<?php

namespace App\Subtitles\VobSub;

use App\Models\SubIdx;

class VobSub2Srt implements VobSub2SrtInterface
{
    private $logToSubIdx = null;
    private $filePathWithoutExtension;
    private $idxFile;

    public function __construct($pathWithoutExtension, $logToSubIdx = null)
    {
        $this->filePathWithoutExtension = $pathWithoutExtension;

        if($logToSubIdx !== null) {
            $this->logToSubIdx = ($logToSubIdx instanceof SubIdx) ? $logToSubIdx : SubIdx::findOrFail($logToSubIdx);
        }

        if(!file_exists("{$this->filePathWithoutExtension}.sub") || !file_exists("{$this->filePathWithoutExtension}.idx")) {
            throw new \Exception("{$this->filePathWithoutExtension}.sub/.idx does not exist");
        }

        $this->idxFile = new IdxFile("{$this->filePathWithoutExtension}.idx");
    }

    public function getLanguages()
    {
        $outputLines = $this->execVobsub2srt("--langlist");

        if(!in_array("Languages:", $outputLines)) {
            return [];
        }

        $languages = [];

        foreach($outputLines as $line) {
            if (preg_match('/^(?<index>\d+): ([a-z]+|\(no id\))$/', $line, $match)) {
                $languages[] = [
                    'index' => $match['index'],
                    'language' => $this->idxFile->getLanguageForIndex($match['index']),
                ];
            }
        }

//        $traditionalChinese = [];
//
//        for($i = 0; $i < count($languages); $i++) {
//            if($languages[$i]['language'] !== 'zh') {
//                continue;
//            }
//
//            $traditionalChinese[] = [
//                'index' => $languages[$i]['index'],
//                'language' => $languages[$i]['language'] . '-Hant',
//            ];
//
//            $languages[$i]['language'] = $languages[$i]['language'] . '-Hans';
//        }
//
//        $languages = array_merge($languages, $traditionalChinese);
//
//        usort($languages, function($a, $b){
//            return $a['index'] <=> $b['index'];
//        });

        return $languages;
    }

    public function extractLanguage($index, $language)
    {
        $outputFilePath = "{$this->filePathWithoutExtension}.srt";

        if(file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }

        $extra = '';

        if($language === 'zh') {
            $extra = ' --tesseract-lang chi_sim';
        }
      //  else if($language === 'zh-Hant') {
      //      $extra = ' --tesseract-lang chi_tra';
      //  }

        $this->execVobsub2srt("--index {$index}{$extra}");

        // It is not guaranteed that this file exists after trying to extract it
        return $outputFilePath;
    }

    private function execVobsub2srt($argument)
    {
        if(empty($argument)) {
            throw new \Exception("Argument can't be empty");
        }

        $timeoutSeconds = 300;

        $startedAtSeconds = time();

        $command = "timeout {$timeoutSeconds} /usr/local/bin/vobsub2srt \"{$this->filePathWithoutExtension}\" {$argument} 2>&1";

        $output = trim(shell_exec($command));

        $commandTimeSeconds = time() - $startedAtSeconds;

        $output .= "\n__timeout limit: {$timeoutSeconds}, command took: {$commandTimeSeconds}";

        if($commandTimeSeconds >= ($timeoutSeconds - 1)) {
            $output .= "\n__error: timeout";
        }

        if($this->logToSubIdx !== null) {
            $this->logToSubIdx->vobsub2srtOutputs()->create([
                'argument' => $argument,
                'index'    => explode(" ", $argument)[1] ?? null,
                'output'   => $output,
            ]);
        }

        return preg_split("/\r\n|\n|\r/", $output);
    }

}
