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

        return $languages;
    }

    public function extractLanguage($index)
    {

    }

    private function execVobsub2srt($argument)
    {
        if(empty($argument)) {
            throw new \Exception("Argument can't be empty");
        }

        $command = "vobsub2srt \"{$this->filePathWithoutExtension}\" {$argument} 2>&1";

        $output = trim(shell_exec($command));

        if($this->logToSubIdx !== null) {
            $this->logToSubIdx->vobsub2srtOutputs()->create([
                'argument' => $argument,
                'index'    => explode("--index ", $argument)[1] ?? null,
                'output'   => $output,
            ]);
        }

        return preg_split("/\r\n|\n|\r/", $output);
    }

}
