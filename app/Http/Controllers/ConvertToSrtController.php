<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertToSrtJob;
use App\Models\FileGroup;
use Illuminate\Http\Request;

class ConvertToSrtController extends Controller
{
    protected $indexRouteName = 'convert-to-srt';

    public function index()
    {
        return view('convert-to-srt');
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'subtitles'   => 'required|array|max:100|uploaded_files',
        ]);

        $files = $request->file('subtitles');

        $fileGroup = FileGroup::create([
            'original_name' => $request->_archiveName ?? null,
            'tool_route' => $this->indexRouteName,
            'url_key' => str_random(16),
        ]);

        if(count($files) === 1) {
            $fileJob = $this->dispatchNow(new ConvertToSrtJob($fileGroup, $files[0]));

            if($fileJob->hasError) {
                return back()->withErrors(["subtitles" => __($fileJob->error_message)]);
            }
        }
        else {
            foreach($files as $file) {
                $this->dispatch(new ConvertToSrtJob($fileGroup, $file));
            }
        }

        return redirect($fileGroup->resultRoute);
    }

    public function result($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->where('tool_route', $this->indexRouteName)
            ->firstOrFail();

        return view('file-group-result', [
            'urlKey' => $urlKey,
            'returnUrl' => route($this->indexRouteName),
            'fileCount' => $fileGroup->fileJobs()->count(),
        ]);
    }
}
