<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Crawl the site and generate a sitemap.xml';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = public_path('sitemap.xml');

        SitemapGenerator::create(config('app.url'))->writeToFile($filePath);

        // remove empty lines manually

        $lines = preg_split("/\r\n|\n|\r/", file_get_contents($filePath));

        $lines = array_filter($lines, function($line) {
           return !empty(trim($line));
        });

        file_put_contents($filePath, implode("\r\n", $lines));
    }
}
