<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupRequest;
use App\Support\Facades\FileHash;
use App\Support\Facades\FileName;
use App\Models\StoredFile;
use App\Models\SupJob;

class SupController extends Controller
{
    public function index()
    {
        return view('guest.sup', [
            'languages' => config('st.tesseract.languages'),
        ]);
    }

    public function post(SupRequest $request)
    {
        $supFile = $request->getSupFile();

        $ocrLanguage = $request->getOcrLanguage();

        $hash = FileHash::make($supFile);

        $supJob = SupJob::query()
            ->where('input_file_hash', $hash)
            ->where('ocr_language', $ocrLanguage)
            ->first();

        if ($supJob === null) {
            $inputFile = StoredFile::getOrCreate($supFile);

            $supJob = SupJob::create([
                'url_key'              => generate_url_key(),
                'input_stored_file_id' => $inputFile->id,
                'input_file_hash'      => $hash,
                'ocr_language'         => $ocrLanguage,
                'original_name'        => basename($supFile->getClientOriginalName()),
            ]);

            $supJob->dispatchJob();
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
