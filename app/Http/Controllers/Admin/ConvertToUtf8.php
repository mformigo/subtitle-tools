<?php

namespace App\Http\Controllers\Admin;

use App\Http\Rules\TextFileRule;
use App\Support\Facades\TempFile;
use App\Support\TextFile\TextEncoding;
use Illuminate\Http\Request;

class ConvertToUtf8
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', new TextFileRule],
            'from_encoding' => 'required|string',
        ]);

        $file = $request->file('file');
        $encoding = $request->get('from_encoding');

        $textEncoding = new TextEncoding();

        $content = file_get_contents($file->getRealPath());

        $converted = $textEncoding->toUtf8($content, $encoding);

        $filePath = TempFile::make($converted);

        return response()->download(
            $filePath,
            $file->getClientOriginalName().'_'.$encoding.'.'.$file->getClientOriginalExtension()
        );
    }
}
