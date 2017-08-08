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
            //'convert-text-files-to-utf8:idx',
        ])->only('post');
    }

    public function index()
    {
        return view('sub-idx');
    }

    public function detail($pageId)
    {
        $subIdx = SubIdx::where('page_id', $pageId)->firstOrFail();

        return view('sub-idx-detail', [
            'originalName' => $subIdx->original_name,
            'pageId' => $pageId,
        ]);
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'sub' => 'required|file|mimetypes:video/mpeg',
            'idx' => 'required|file|textfile',
        ], [
            'sub.mimetypes' => __('validation.subidx_invalid_sub_mime'),
        ]);

        $subIdx = SubIdx::getOrCreateFromUpload($request->file('sub'), $request->file('idx'));

        if(!$subIdx->is_readable) {
            return back()->withErrors(__("validation.subidx_cant_be_read"));
        }

        return redirect()->route('sub-idx-detail', ['pageId' => $subIdx->page_id]);
    }

    public function downloadSrt($pageId, $languageIndex)
    {
        throw new \Exception("not implemented yet");
    }

}
