<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertToSrtJob;
use App\Models\FileGroup;
use Illuminate\Http\Request;

class ConvertToSrtController extends Controller
{
    protected $toolIndexRoute = 'convert-to-srt';

    public function index()
    {
        return view('convert-to-srt');
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

        return redirect()->route("{$this->toolIndexRoute}-result", ['urlKey' => $fileGroup->url_key]);
    }

    public function result($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->where('tool_route', $this->toolIndexRoute)
            ->firstOrFail();

        return view('file-group-result', [
            'returnUrl' => route($this->toolIndexRoute),
            'fileCount' => $fileGroup->fileJobs()->count(),
        ]);
    }
}
