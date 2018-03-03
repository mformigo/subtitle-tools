<?php

namespace App\Subtitles\Tools\Options;

use Illuminate\Http\Request;

class SrtCleanerOptions extends ToolOptions
{
    public $stripCurly = false; // { }

    public $stripAngle = false; // < >

    public $stripSquare = false; // [ ]

    public $stripParentheses = false; // ( )

    public function loadRequest(Request $request)
    {
        return $this->load([
            'stripCurly'       => $request->has('stripCurly')       && (bool) $request->get('stripCurly'),
            'stripAngle'       => $request->has('stripAngle')       && (bool) $request->get('stripAngle'),
            'stripSquare'      => $request->has('stripSquare')      && (bool) $request->get('stripSquare'),
            'stripParentheses' => $request->has('stripParentheses') && (bool) $request->get('stripParentheses'),
        ]);
    }
}
