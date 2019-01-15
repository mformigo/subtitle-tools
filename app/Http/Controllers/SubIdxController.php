<?php

namespace App\Http\Controllers;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubMimeRule;
use App\Http\Rules\TextFileRule;
use App\Models\SubIdx;
use Illuminate\Http\Request;

class SubIdxController
{
    public function index()
    {
        return view('tools.convert-sub-idx-to-srt');
    }

    public function post(Request $request)
    {
        $request->validate([
            'sub' => ['required', 'file', new FileNotEmptyRule, new SubMimeRule],
            'idx' => ['required', 'file', new FileNotEmptyRule, new TextFileRule],
        ]);

        $subIdx = SubIdx::getOrCreateFromUpload(
            $request->files->get('sub'),
            $request->files->get('idx')
        );

        if (! $subIdx->is_readable) {
            return back()->withErrors('The sub/idx file can not be read');
        }

        return redirect()->route('subIdx.show', $subIdx->page_id);
    }

    public function detail($pageId)
    {
        $subIdx = SubIdx::where('page_id', $pageId)->firstOrFail();

        $languageCount = $subIdx->languages()->count();

        return view('tool-results.sub-idx-result', [
            'originalName' => $subIdx->original_name,
            'languageCount' => $languageCount,
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
