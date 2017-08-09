<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConvertToSrtController extends Controller
{
    public function index()
    {
        return view('convert-to-srt-index');
    }

    public function post(Request $request)
    {
        // TODO: if its an archive, send to other function
        return $this->postSubtitle($request);
    }

    private function postSubtitle(Request $request)
    {
        $this->validate($request, [
            'subtitle' => 'required|file|file_not_empty|textfile',
        ]);

        // if filehash is in cache
        //      send to download page


        // $subtitle = TextFileFormat::getMatchingFormat($request->get('subtitle'));

        // if(! $subtitle instanceof TransformToGenericSubtitle) {
        //     back()->withError('cant convert');
        // }

        // $srt = new Srt($subtitle->toGenericSubtitle());



        dd($request);
    }

    private function postArchive(Request $request)
    {
        dd($request);
    }
}
