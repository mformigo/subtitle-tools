<?php

namespace App\Http\Controllers;

use App\Models\FileGroup;
use Illuminate\Http\Request;

abstract class FileJobController extends Controller
{
    public abstract function index();

    public abstract function post(Request $request);

    public function result($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->where('tool_route', $this->getIndexRouteName())
            ->firstOrFail();

        return view('file-group-result', [
            'urlKey' => $urlKey,
            'returnUrl' => route($this->getIndexRouteName()),
            'fileCount' => $fileGroup->fileJobs()->count(),
        ]);
    }

    public function validateFileJob(array $rules = [])
    {
        $rules['subtitles'] = 'required|array|max:100|uploaded_files';

        $this->validate(request(), $rules);
    }

    protected function doFileJobs($jobClass, array $jobOptions = [], $alwaysQueue = false)
    {
        $fileGroup = FileGroup::create([
            'original_name' => $request->_archiveName ?? null,
            'tool_route' => $this->getIndexRouteName(),
            'url_key' => str_random(16),
            'job_options' => $jobOptions,
        ]);

        $files = request()->file('subtitles');

        if($alwaysQueue || count($files) > 1) {
            foreach($files as $file) {
                $this->dispatch(new $jobClass($fileGroup, $file));
            }
        }
        else {
            $fileJob = $this->dispatchNow(new $jobClass($fileGroup, $files[0]));

            if($fileJob->hasError) {
                return back()->withErrors(["subtitles" => __($fileJob->error_message)]);
            }
        }

        return redirect($fileGroup->resultRoute);
    }

    protected abstract function getIndexRouteName();
}
