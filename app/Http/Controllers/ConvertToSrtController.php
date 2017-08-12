<?php

namespace App\Http\Controllers;

use App\Facades\TextFileFormat;
use App\Jobs\ConvertToSrtJob;
use App\StoredFile;
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

        $storedFile = StoredFile::getOrCreate($request->file('subtitle'));

        $inputSubtitle = TextFileFormat::getMatchingFormat($storedFile->filePath);

        if(!$inputSubtitle instanceof TransformsToGenericSubtitle) {
            back()->withErrors('cant convert');
        }

        $textFileJob = $this->dispatchNow(
            new ConvertToSrtJob(
                $storedFile,
                $request->file('subtitle')->getClientOriginalName()
            )
        );

        dd($textFileJob);

        dd('end of controller');
    }

    private function postArchive(Request $request)
    {
        dd($request);
    }
}
