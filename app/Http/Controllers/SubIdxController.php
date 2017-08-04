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
        $subIdx = SubIdx::where(['page_id' => $postId])->firstOrFail();

        return view('sub-idx-detail');
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
            $pageId = SubIdx::getCachedPageId($subFile->path(), $idxFile->path());
        }
        else {
            $pageId = SubIdx::createNewFromUpload($subFile, $idxFile)->page_id;
        }

        return redirect()->route('sub-idx-detail', [
            'pageId' => $pageId,
        ]);
    }
}
