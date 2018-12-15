<?php

namespace App\Http\Controllers\FileJobs;

use App\Http\Controllers\Controller;
use App\Subtitles\Tools\Options\NoOptions;
use App\Subtitles\Tools\Options\ToolOptions;
use App\Support\Facades\FileName;
use App\Http\Rules\AreUploadedFilesRule;
use App\Models\FileGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class FileJobController extends Controller
{
    protected $indexRouteName;

    protected $job;

    /**
     * @var ToolOptions
     */
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

        return $this->doFileJobs($this->job, $options);
    }

    protected function rules(): array
    {
        return [];
    }

    protected function validateFileJob(Request $request, array $additionalRules = [])
    {
        // Allow the additional rules to override the "subtitles" rule.
        $request->validate($additionalRules + [
            'subtitles' => ['required', 'array', 'max:100', new AreUploadedFilesRule],
        ]);
    }

    protected function options(Request $request)
    {
        return $this->options->load($request);
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

    protected function doFileJobs($jobClass, array $jobOptions = [])
    {
        $files = array_wrap(
            request()->files->get('subtitles')
        );

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
                return back()->withInput()->withErrors(['subtitles' => __($fileJob->error_message)]);
            }
        }

        return redirect($fileGroup->resultRoute);
    }
}
