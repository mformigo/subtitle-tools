<?php

namespace App\Console\Commands;

use App\Models\SubIdx;
use App\Models\SupJob;
use Illuminate\Console\Command;

class RandomizeUrlKeys extends Command
{
    protected $signature = 'st:randomize-url-keys';

    protected $description = 'Change url keys of sub/idx and sup pages';

    public function handle()
    {
        $this->output->writeln('Randomizing url keys... ');

        $notUpdatedSince = now()->subHours(36);

        $this->changeSubIdxUrlKeys($notUpdatedSince);

        $this->changeSupUrlKeys($notUpdatedSince);

        $this->info('Done!');
    }

    protected function changeSubIdxUrlKeys($notUpdatedSince)
    {
        SubIdx::query()
            ->whereDate('updated_at', '<', $notUpdatedSince)
            ->get()
            ->tap(function ($collection) {
                $this->output->writeln('Randomizing '.count($collection).' sub/idx url keys...');
            })
            ->each(function (SubIdx $subIdx) {
                $subIdx->update(['url_key' => generate_url_key()]);
            });
    }

    protected function changeSupUrlKeys($notUpdatedSince)
    {
        SupJob::query()
            ->whereDate('updated_at', '<', $notUpdatedSince)
            ->get()
            ->tap(function ($collection) {
                $this->output->writeln('Randomizing '.count($collection).' sup url keys...');
            })
            ->each(function (SupJob $supJob) {
                $supJob->update(['url_key' => generate_url_key()]);
            });
    }
}
