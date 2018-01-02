<?php

namespace App\Subtitles\PlainText;

use SjorsO\TextFile\Facades\TextFileReader;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MicroDVD extends TextFile implements TransformsToGenericSubtitle
{
    use WithFileLines;

    protected $extension = "sub";

    protected $frameRate = 23.976;

    public function __construct($source = null)
    {
        if ($source !== null) {
            $this->loadFile($source);
        }
    }

    public function loadFile($file)
    {
        parent::loadFile($file);

        if (count($this->lines) > 0 && MicroDVDCue::isTimingString($this->lines[0])) {
            $firstCue = new MicroDVDCue($this->lines[0]);

            $maybeFpsHint = $firstCue->getLines()[0] ?? "NO HINT";

            if (preg_match('/^(?<fps>\d\d(\.|,)\d+)$/', $maybeFpsHint, $matches)) {
                $hintedFps = str_replace(',', '.', $matches['fps']);

                $this->setFps($hintedFps);
            }
        }

        return $this;
    }

    public function setFps($fps)
    {
        if (!is_float($fps) && !preg_match('/\d\d\.\d+/', $fps)) {
            throw new \Exception("Invalid framerate ({$fps})");
        }

        $this->frameRate = (float)$fps;
    }

    public function getFps()
    {
        return $this->frameRate;
    }

    /**
     * Returns true if the $filePath file is a valid format for this class
     * @param $file
     * @return bool
     */
    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = TextFileReader::getLines($filePath);

        $validCues = 0;

        foreach ($lines as $line) {
            if (MicroDVDCue::isTimingString($line)) {
                $validCues++;

                if ($validCues === 3) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach ($this->lines as $line) {
            if (MicroDVDCue::isTimingString($line)) {
                $microDvdCue = new MicroDVDCue($line);

                $microDvdCue->setFps($this->getFps());

                $genericCue = $microDvdCue->toGenericCue();

                $generic->addCue($genericCue);
            }
        }

        return $generic;
    }
}
