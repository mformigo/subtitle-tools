<?php

namespace Tests;

use App\Models\FileGroup;

trait PostsFileJobs
{
    protected $fileJobsPosted = 0;

    private function postFileJob(string $routeName, array $subtitles, array $extraData = [])
    {
        $this->withoutEvents();

        $response = $this->post(route($routeName), [
            'subtitles' => $subtitles,
        ] + $extraData);

        $fileGroup = FileGroup::findOrFail(++$this->fileJobsPosted);

        return [$response, $fileGroup];
    }
}
