<?php

namespace App\Subtitles\VobSub;

use App\Models\SubIdx;

class VobSub2Srt
{
    private $subIdx;
    private $idxFile;

    public function __construct(SubIdx $subIdx)
    {
        if(!file_exists("{$subIdx->filePathWithoutExtension}.sub") || !file_exists("{$subIdx->filePathWithoutExtension}.idx")) {
            throw new \Exception("{$subIdx->filePathWithoutExtension}.sub/.idx does not exist");
        }

        $this->subIdx = $subIdx;

        $this->idxFile = new IdxFile("{$subIdx->filePathWithoutExtension}.idx");
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

        $command = "vobsub2srt \"{$this->subIdx->filePathWithoutExtension}\" {$argument} 2>&1";

        $output = trim(shell_exec($command));

        $this->subIdx->vobsub2srtOutputs()->create([
            'argument' => $argument,
            'index'    => explode("--index ", $argument)[1] ?? null,
            'output'   => $output,
        ]);

        return preg_split("/\r\n|\n|\r/", $output);
    }

}
