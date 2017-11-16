<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    protected $signature = 'st:generate-sitemap';

    protected $description = 'Crawl the site and generate a sitemap.xml';

    public function handle()
    {
        $this->output->write('Generating sitemap... ');

        $applicationUrl = config('app.url');

        $sitemapFilePath = public_path('sitemap.xml');

        SitemapGenerator::create($applicationUrl)->writeToFile($sitemapFilePath);

        $this->info('Done!');
    }
}
