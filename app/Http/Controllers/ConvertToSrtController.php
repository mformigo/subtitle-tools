<?php

namespace App\Http\Controllers;

use App\Facades\TextFileFormat;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TransformsToGenericSubtitle;
use Illuminate\Http\Request;

class ConvertToSrtController extends Controller
{
    public function index()
    {
        return view('convert-to-srt-index');
    }

    public function post(Request $request)
    {
        // if filehash is in cache
        //      send to download page

        // TODO: if its an archive, send to other function
        return $this->postSubtitle($request);
    }

    private function postSubtitle(Request $request)
    {
        $this->validate($request, [
            'subtitle' => 'required|file|file_not_empty|textfile',
        ]);

        $inputSubtitle = TextFileFormat::getMatchingFormat($request->file('subtitle'));

        if(!$inputSubtitle instanceof TransformsToGenericSubtitle) {
            back()->withErrors('cant convert');
        }

        $srt = new Srt($inputSubtitle);

        $srt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        dd($srt);
    }

    private function postArchive(Request $request)
    {
        dd($request);
    }
}
