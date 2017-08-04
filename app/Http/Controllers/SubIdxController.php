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

    public function detail($postId)
    {
        $subIdx = SubIdx::where([
            'page_id' => $postId,
            'is_readable' => true,
        ])->firstOrFail();

        return view('sub-idx-detail');
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'sub' => 'required|file|mimetypes:video/mpeg',
            'idx' => 'required|file|textfile',
        ], [
            'sub.mimetypes' => trans('validation.subidx_invalid_sub_mime'),
        ]);

        $subFile = $request->file('sub');
        $idxFile = $request->file('idx');

        if(SubIdx::isCached($subFile->path(), $idxFile->path())) {
            $subIdx = SubIdx::getFromCache($subFile->path(), $idxFile->path());
        }
        else {
            $subIdx = SubIdx::createNewFromUpload($subFile, $idxFile);
        }

        if(!$subIdx->isReadable()) {
            return back()->withErrors(trans("validation.subidx_cant_be_read"));
        }

        return redirect()->route('sub-idx-detail', [
            'pageId' => $subIdx->page_id,
        ]);
    }
}
