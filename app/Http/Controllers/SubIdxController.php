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

        $subFilePath = $request->files->get('sub')->getRealPath();
        $idxFilePath = $request->files->get('idx')->getRealPath();

        if(SubIdx::isCached($subFilePath, $idxFilePath)) {
            redirect()->route('sub-idx', ['page_id' => SubIdx::getCachedPageId($subFilePath, $idxFilePath)]);
        }

        $subIdx = SubIdx::createNewFromUpload($request->files->get('sub'), $request->files->get('idx'));

        dd($subIdx);



        // Couldn't open VobSub files 'fake.idx/.sub'
        //        Languages:
        //        0: en


        dd($request);
    }
}
