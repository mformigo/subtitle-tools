<?php

namespace App\Http\Controllers;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubMimeRule;
use App\Http\Rules\TextFileRule;
use App\Models\SubIdx;
use Illuminate\Http\Request;

class SubIdxController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'swap-sub-and-idx',
        ])->only('post');
    }

    public function index()
    {
        return view('guest.sub-idx');
    }

    public function post(Request $request)
    {
        $request->validate([
            'sub' => ['required', 'file', new FileNotEmptyRule, new SubMimeRule ],
            'idx' => ['required', 'file', new FileNotEmptyRule, new TextFileRule],
        ]);

        $subIdx = SubIdx::getOrCreateFromUpload($request->files->get('sub'), $request->files->get('idx'));

        if(!$subIdx->is_readable) {
            return back()->withErrors(__("validation.subidx_cant_be_read"));
        }

        return redirect()->route('subIdxDetail', ['pageId' => $subIdx->page_id]);
    }

    public function detail($pageId)
    {
        $subIdx = SubIdx::where('page_id', $pageId)->firstOrFail();

        return view('guest.sub-idx-detail', [
            'originalName' => $subIdx->original_name,
            'pageId' => $pageId,
        ]);
    }

    public function downloadSrt($pageId, $languageIndex)
    {
        $subIdxLanguage = SubIdx::query()
            ->where('page_id', $pageId)
            ->firstOrFail()
            ->languages()
            ->where('index', $languageIndex)
            ->whereNull('error_message')
            ->whereNotNull('finished_at')
            ->firstOrFail();

        $filePath = $subIdxLanguage->filePath;

        $fileName = $subIdxLanguage->fileName;

        return response()->download($filePath, $fileName);
    }

}
