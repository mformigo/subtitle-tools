<?php

namespace Tests;

use App\Models\FileGroup;

trait CreatesFileGroups
{
    /**
     * @param string $toolRoute
     * @param null $urlKey
     * @return FileGroup
     */
    private function createFileGroup($toolRoute = "default-route", $urlKey = null)
    {
        $fileGroup = new FileGroup();

        $fileGroup->fill([
            'tool_route' => $toolRoute,
            'url_key' => $urlKey ?? generate_url_key(),
        ])->save();

        return $fileGroup;
    }
}
