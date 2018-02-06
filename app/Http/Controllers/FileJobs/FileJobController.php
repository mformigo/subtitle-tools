<?php

namespace App\Http\Controllers\FileJobs;

use App\Http\Controllers\Controller;
use App\Support\Facades\FileName;
use App\Http\Rules\AreUploadedFilesRule;
use App\Models\FileGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LogicException;

abstract class FileJobController extends Controller
{
    protected $indexRouteName;

    protected $job;

    protected $shouldAlwaysQueue = false;

    public function __construct()
    {
        $this->middleware(['check-file-size', 'extract-archives'])->only('post');

        if (! $this->indexRouteName || ! $this->job) {
            throw new LogicException('You should define both "$indexRouteName" and "$job" on the FileJobController');
        }
    }

    abstract public function index();

    public function post(Request $request)
    {
        $this->validateFileJob(
            $request,
            $this->rules()
        );

        $options = $this->options($request);

        if ($options instanceof RedirectResponse) {
            return $options;
        }

        return $this->doFileJobs($this->job, $options);
    }

    protected function rules(): array
    {
        return [];
    }

    public function validateFileJob(Request $request, array $additionalRules = [])
    {
        $request->validate([
            'subtitles' => ['required', 'array', 'max:100', new AreUploadedFilesRule],
        ] + $additionalRules);
    }

    protected function options(Request $request)
    {
        return [];
    }

    public function result($urlKey)
    {
        $fileGroup = FileGroup::findForTool($urlKey, $this->indexRouteName);

        return view('tool-results.file-group-result', [
            'urlKey'    => $urlKey,
            'returnUrl' => route($this->indexRouteName),
            'fileCount' => $fileGroup->fileJobs()->count(),
        ]);
    }

    public function download($urlKey, $id)
    {
        $fileJob = FileGroup::findForTool($urlKey, $this->indexRouteName)
            ->fileJobs()
            ->whereNotNull('finished_at')
            ->whereNull('error_message')
            ->findOrFail($id);

        // The original name can contain a path if it came from an archive file
        $name = basename($fileJob->originalNameWithNewExtension);

        // Adding a watermark also helps guarantee that the file name has some
        // ascii chars. If the name does not contain any ascii chars, the
        // download response could strip the whole name.
        $name = FileName::watermark($name);

        return response()->download($fileJob->outputStoredFile->file_path, $name);
    }

    protected function doFileJobs($jobClass, array $jobOptions = [])
    {
        $files = request()->files->get('subtitles');

        // only for safety. middleware should ensure this is never true
        if (count($files) === 0) {
            return back()->withErrors(['subtitles' => __('validation.unknown_error')]);
        }

        $fileGroup = FileGroup::create([
            'tool_route'  => $this->indexRouteName,
            'url_key'     => generate_url_key(),
            'job_options' => $jobOptions,
        ]);

        if ($this->shouldAlwaysQueue || count($files) > 1) {
            foreach ($files as $file) {
                $this->dispatch(new $jobClass($fileGroup, $file));
            }
        } else {
            // need to use array_values because we mess up the keys somewhere
            $fileJob = $this->dispatchNow(new $jobClass($fileGroup, array_values($files)[0]));

            if ($fileJob === null) {
                return back(); // fileJob is null when testing with ->withoutJobs();
            }

            if ($fileJob->hasError) {
                return back()->withErrors(['subtitles' => __($fileJob->error_message)]);
            }
        }

        return redirect($fileGroup->resultRoute);
    }
}
