<?php

namespace App\Http\Controllers;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubMimeRule;
use App\Http\Rules\TextFileRule;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Database\Eloquent\Builder;
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

        return redirect()->route('subIdx.show', $subIdx->url_key);
    }

    public function show($urlKey)
    {
        $subIdx = SubIdx::where('url_key', $urlKey)->firstOrFail();

        return view('tool-results.sub-idx-result', [
            'originalName' => $subIdx->original_name,
            'urlKey' => $urlKey,
        ]);
    }

    public function downloadSrt($urlKey, $index)
    {
        $language = SubIdxLanguage::query()
            ->with('outputStoredFile')
            ->where('index', $index)
            ->whereNull('error_message')
            ->whereNotNull('finished_at')
            ->whereHas('subIdx', function (Builder $query) use ($urlKey) {
                $query->where('url_key', $urlKey);
            })
            ->firstOrFail();

        if (! $language->outputStoredFile) {
            abort(404);
        }

        return response()->download($language->outputStoredFile->file_path, $language->file_name);
    }

    public function downloadRedirect($urlKey, $index)
    {
        return redirect()->route('subIdx.show', $urlKey);
    }
}
