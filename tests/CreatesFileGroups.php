<?php

namespace Tests;

use App\Models\FileGroup;

trait CreatesFileGroups
{
    /**
     * @param string $toolRoute
     * @param null $originalName
     * @param null $urlKey
     * @return FileGroup
     */
    private function createFileGroup($toolRoute = "default-route", $originalName = null, $urlKey = null)
    {
        $fileGroup = new FileGroup();

        $fileGroup->fill([
            'original_name' => $originalName ?? null,
            'tool_route' => $toolRoute,
            'url_key' => $urlKey ?? str_random(16),
        ])->save();

        return $fileGroup;
    }
}
