<?php

namespace App\Console\Commands;

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

        $this->changeSupUrlKeys($notUpdatedSince);

        $this->info('Done!');
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
