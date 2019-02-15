<?php

namespace App\Http\Controllers;

use App\Http\Rules\AreUploadedFilesRule;
use App\Jobs\FileJobs\FileJob;
use App\Models\FileGroup;
use App\Models\FileJob as FileJobModel;
use App\Subtitles\Tools\Options\NoOptions;
use App\Subtitles\Tools\Options\ToolOptions;
use App\Support\Facades\FileName;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class FileJobController extends BaseController
{
    use DispatchesJobs;

    protected $indexRouteName;

    /** @var FileJob $job */
    protected $job;

    /** @var ToolOptions $options */
    protected $options = NoOptions::class;

    protected $shouldAlwaysQueue = false;

    protected $extractArchives = true;

    public function __construct()
    {
        $this->middleware('check-file-size')->only('post');

        if ($this->extractArchives) {
            $this->middleware('extract-archives')->only('post');
        }

        $this->options = new $this->options;
    }

    abstract public function index();

    public function post(Request $request)
    {
        $this->validateFileJob(
            $request,
            $this->rules() + $this->options->rules()
        );

        $options = $this->options($request);

        if ($options instanceof RedirectResponse) {
            return $options;
        }

        if ($options instanceof ToolOptions) {
            $options = $options->toArray();
        }

        return $this->doFileJobs($options);
    }

    protected function rules(): array
    {
        return [];
    }

    protected function validateFileJob(Request $request, array $additionalRules = [])
    {
        $request->validate($additionalRules + [
            'subtitles' => ['required', 'array', 'max:100', new AreUploadedFilesRule],
        ]);
    }

    protected function options(Request $request)
    {
        return $this->options->load($request);
    }

    protected function doFileJobs(array $jobOptions = [])
    {
        $files = array_wrap(
            request()->files->get('subtitles')
        );

        $fileGroup = FileGroup::create([
            'tool_route' => $this->indexRouteName,
            'url_key' => generate_url_key(),
            'job_options' => $jobOptions,
        ]);

        return $this->shouldAlwaysQueue || count($files) > 1
            ? $this->queueFileJobs($fileGroup, $files)
            : $this->processFileJob($fileGroup, Arr::first($files));
    }

    private function queueFileJobs(FileGroup $fileGroup, $files)
    {
        $fileJobModels = [];

        foreach ($files as $file) {
            $fileJobModels[] = FileJobModel::makeFromUploadedFile($file);
        }

        $fileGroup->fileJobs()->saveMany($fileJobModels);

        foreach ($fileJobModels as $fileJobModel) {
            $this->job::dispatch($fileJobModel);
        }

        return redirect($fileGroup->result_route);
    }

    private function processFileJob(FileGroup $fileGroup, UploadedFile $file)
    {
        $fileGroup->fileJobs()->save(
            $fileJobModel = FileJobModel::makeFromUploadedFile($file)
        );

        $job = new $this->job($fileJobModel);

        /** @var \App\Models\FileJob $fileJob */
        $fileJob = $job->handle();

        return $fileJob->has_error
            ? back()->withInput()->withErrors(['subtitles' => __($fileJob->error_message)])
            : redirect($fileGroup->result_route);
    }

    public function result($urlKey)
    {
        $fileGroup = FileGroup::findForTool($urlKey, $this->indexRouteName);

        return view('tool-results.file-group-result', [
            'urlKey' => $urlKey,
            'returnUrl' => route($this->indexRouteName),
            'fileCount' => $fileGroup->fileJobs()->count(),
        ]);
    }

    public function download($urlKey, $id)
    {
        /** @var \App\Models\FileJob $fileJob */
        $fileJob = FileGroup::findForTool($urlKey, $this->indexRouteName)
            ->fileJobs()
            ->whereNotNull('finished_at')
            ->whereNull('error_message')
            ->findOrFail($id);

        // The original name can contain a path if it came from
        // an archive file. Use "class_basename" because some
        // archives use backslashes as directory separators.
        $name = class_basename($fileJob->original_name_with_new_extension);

        // Adding a watermark also helps guarantee that the file name has some
        // ascii chars. If the name does not contain any ascii chars, the
        // download response could strip the whole name.
        $name = FileName::watermark($name);

        // TODO: test + handle this neatly in the query above
        // Sometimes there is no "outputStoredFile" at this point.
        if (! $fileJob->outputStoredFile) {
            abort(404);
        }

        return response()->download($fileJob->outputStoredFile->file_path, $name);
    }

    public function downloadRedirect($urlKey, $id)
    {
        $resultRoute = route($this->indexRouteName.'.result', $urlKey);

        return redirect($resultRoute);
    }
}
