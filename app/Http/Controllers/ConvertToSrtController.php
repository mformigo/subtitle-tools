<?php

namespace App\Http\Controllers;

use App\Facades\TextFileFormat;
use App\Jobs\ConvertToSrtJob;
use App\Models\FileGroup;
use App\Models\StoredFile;
use App\Subtitles\TransformsToGenericSubtitle;
use Illuminate\Http\Request;

class ConvertToSrtController extends Controller
{
    protected $toolIndexRoute = 'convert-to-srt';

    public function index()
    {
        return view($this->toolIndexRoute);
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'subtitles'   => 'required',
            'subtitles.*' => 'file',
        ], [
            'subtitles.*.file' => __('validation.one_or_more_failed_subtitle_upload'),
        ]);

        $files = $request->file('subtitles');

        $fileGroup = FileGroup::create([
            'original_name' => $request->_archiveName ?? null,
            'tool_route' => $this->toolIndexRoute,
            'url_key' => str_random(16),
        ]);

        if(count($files) === 1) {
            $fileJob = $this->dispatchNow(new ConvertToSrtJob($fileGroup, $files[0]));

            if($fileJob->hasError) {
                return back()->withErrors($fileJob->error_message);
            }
        }
        else {
            foreach($files as $file) {
                $this->dispatch(new ConvertToSrtJob($fileGroup, $file));
            }
        }

        return redirect()->route("{$this->toolIndexRoute}-download", ['urlKey' => $fileGroup->url_key]);
    }
}
