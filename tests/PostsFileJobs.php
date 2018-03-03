<?php

namespace Tests;

use App\Models\FileGroup;
use App\Subtitles\Tools\Options\ToolOptions;

trait PostsFileJobs
{
    protected $fileJobsPosted = 0;

    private function postFileJob(string $routeName, array $subtitles, $options = [])
    {
        $this->withoutEvents();

        $options = $options instanceof ToolOptions
            ? $options->toArray()
            : $options;

        $response = $this->post(route($routeName), $options + [
            'subtitles' => $subtitles,
        ]);

        $fileGroup = FileGroup::findOrFail(++$this->fileJobsPosted);

        return [$response, $fileGroup];
    }
}
