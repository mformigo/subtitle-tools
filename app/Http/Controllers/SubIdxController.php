<?php

namespace App\Http\Controllers;

use App\Models\SubIdx;
use Illuminate\Http\Request;

class SubIdxController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'swap-sub-and-idx',
        //    'convert-text-files-to-utf8:idx',
        ])->only('post');
    }

    public function index()
    {
        return view('sub-idx');
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'sub' => 'required|file|mimetypes:video/mpeg',
            'idx' => 'required|file|textfile',
        ], [
            'sub.mimetypes' => trans('validation.sub_invalid_mime'),
        ]);

        $subFile = $request->file('sub');
        $idxFile = $request->file('idx');

        if(SubIdx::isCached($subFile->path(), $idxFile->path())) {
            redirect()->route('sub-idx', ['page_id' => SubIdx::getCachedPageId($subFile->path(), $idxFile->path())]);
        }

        $subIdx = SubIdx::createNewFromUpload($subFile, $idxFile);

        dd($subIdx);



        // Couldn't open VobSub files 'fake.idx/.sub'
        //        Languages:
        //        0: en


        dd($request);
    }
}
