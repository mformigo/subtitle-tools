<?php

namespace App\Subtitles\Tools\Options;

use Illuminate\Http\Request;

class SrtCleanerOptions extends ToolOptions
{
    public $stripCurly = false; // { }

    public $stripAngle = false; // < >

    public $stripSquare = false; // [ ]

    public $stripParentheses = false; // ( )

    public $stripSpeakerLabels = false;

    public $stripCuesWithMusicNote = false;

    public function loadRequest(Request $request)
    {
        return $this->load([
            'stripCurly' => (bool) $request->get('stripCurly'),
            'stripAngle' => (bool) $request->get('stripAngle'),
            'stripSquare' => (bool) $request->get('stripSquare'),
            'stripParentheses' => (bool) $request->get('stripParentheses'),
            'stripSpeakerLabels' => (bool) $request->get('stripSpeakerLabels'),
            'stripCuesWithMusicNote' => (bool) $request->get('stripCuesWithMusicNote'),
        ]);
    }

    public function stripCurly($bool = true)
    {
        $this->stripCurly = (bool) $bool;

        return $this;
    }

    public function stripAngle($bool = true)
    {
        $this->stripAngle = (bool) $bool;

        return $this;
    }

    public function stripSquare($bool = true)
    {
        $this->stripSquare = (bool) $bool;

        return $this;
    }

    public function stripParentheses($bool = true)
    {
        $this->stripParentheses = (bool) $bool;

        return $this;
    }

    public function stripSpeakerLabels($bool = true)
    {
        $this->stripSpeakerLabels = (bool) $bool;

        return $this;
    }

    public function stripCuesWithMusicNote($bool = true)
    {
        $this->stripCuesWithMusicNote = (bool) $bool;

        return $this;
    }
}
