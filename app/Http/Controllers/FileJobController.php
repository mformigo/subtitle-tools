<?php

namespace App\Http\Controllers;

use App\Facades\FileName;
use App\Http\Rules\AreUploadedFilesRule;
use App\Models\FileGroup;
use Illuminate\Http\Request;

abstract class FileJobController extends Controller
{
    public abstract function index();

    public abstract function post(Request $request);

    protected abstract function getIndexRouteName();

    public function __construct()
    {
        $this->middleware([
            'check-file-size',
            'extract-archives',
        ])->only('post');
    }

    public function result($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->where('tool_route', $this->getIndexRouteName())
            ->firstOrFail();

        return view('guest.file-group-result', [
            'urlKey' => $urlKey,
            'returnUrl' => route($this->getIndexRouteName()),
            'fileCount' => $fileGroup->fileJobs()->count(),
        ]);
    }

    public function download($urlKey, $id)
    {
        $fileJob = FileGroup::query()
            ->where('url_key', $urlKey)
            ->where('tool_route', $this->getIndexRouteName())
            ->firstOrFail()
            ->fileJobs()
            ->whereNotNull('finished_at')
            ->whereNull('error_message')
            ->findOrFail($id);

        // basename because it can contain the path if it came from a zip file
        $name = basename($fileJob->originalNameWithNewExtension);

        // the name needs to have some ascii chars, else the download response could strip the whole name
        $name = FileName::watermark($name);

        return response()->download($fileJob->outputStoredFile->filePath, $name);
    }

    public function validateFileJob(array $rules = [])
    {
        $rules['subtitles'] = ['required', 'array', 'max:100', new AreUploadedFilesRule];

        request()->validate($rules);
    }

    protected function doFileJobs($jobClass, array $jobOptions = [], $alwaysQueue = false)
    {
        $files = request()->files->get('subtitles');

        // this should never be true
        if(count($files) === 0) {
            return back()->withErrors(["subtitles" => __('validation.unknown_error')]);
        }

        $fileGroup = FileGroup::create([
            'tool_route' => $this->getIndexRouteName(),
            'url_key' => generate_url_key(),
            'job_options' => $jobOptions,
        ]);

        if($alwaysQueue || count($files) > 1) {
            foreach($files as $file) {
                $this->dispatch(new $jobClass($fileGroup, $file));
            }
        }
        else {
            // need to use array_values because we mess up the keys somewhere
            $fileJob = $this->dispatchNow(new $jobClass($fileGroup, array_values($files)[0]));

            if($fileJob === null) {
                return back(); // fileJob is null when testing with ->withoutJobs();
            }

            if($fileJob->hasError) {
                return back()->withErrors(["subtitles" => __($fileJob->error_message)]);
            }
        }

        return redirect($fileGroup->resultRoute);
    }
}
