<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemapJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $applicationUrl = config('app.url');

        $sitemapFilePath = public_path('sitemap.xml');

        SitemapGenerator::create($applicationUrl)->writeToFile($sitemapFilePath);
    }
}
