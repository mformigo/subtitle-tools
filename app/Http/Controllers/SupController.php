<?php

namespace App\Http\Controllers;

use App\Support\Facades\FileName;
use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SupRule;
use App\Jobs\SupToSrtJob;
use App\Models\StoredFile;
use App\Models\SupJob;
use Illuminate\Http\Request;

class SupController extends Controller
{
    public function index()
    {
        $languages = config('st.tesseract.languages');

        return view('guest.sup')->with('languages', $languages);
    }

    public function post(Request $request)
    {
        $request->validate([
            'subtitle'    => ['bail', 'required', 'file', new FileNotEmptyRule, new SupRule],
            'ocrLanguage' => 'required|in:'.implode(',', config('st.tesseract.languages')),
        ]);

        $supFile = $request->file('subtitle');

        $ocrLanguage = $request->get('ocrLanguage');

        $inputFile = StoredFile::getOrCreate($supFile);

        $supJob = SupJob::query()
            ->where('input_stored_file_id', $inputFile->id)
            ->where('ocr_language', $ocrLanguage)
            ->first();

        if($supJob === null) {
            $supJob = SupJob::create([
                'url_key'              => generate_url_key(),
                'input_stored_file_id' => $inputFile->id,
                'ocr_language'         => $ocrLanguage,
                'original_name'        => basename($supFile->getClientOriginalName()),
            ]);

            SupToSrtJob::dispatch($supJob)->onQueue('slow-high');
        }

        return redirect()->route('sup.show', $supJob->url_key);
    }

    public function show($urlKey)
    {
        $supJob = SupJob::where('url_key', $urlKey)->firstOrFail();

        return view('guest.supShow', [
            'originalName' => $supJob->original_name,
            'ocrLanguage'  => $supJob->ocr_language,
            'urlKey'       => $urlKey,
            'returnUrl'    => route('sup'),
        ]);
    }

    public function download($urlKey)
    {
        $supJob = SupJob::query()
            ->where('url_key', $urlKey)
            ->whereNull('error_message')
            ->whereNotNull('finished_at')
            ->firstOrFail();

        $filePath = $supJob->outputStoredFile->file_path;

        $fileName = FileName::changeExtension($supJob->original_name, 'srt');

        return response()->download($filePath, $fileName);
    }
}
