<?php

namespace App\Jobs;

use App\Models\SubIdxLanguage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExtractSubIdxLanguage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $subIdxLanguage;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->subIdxLanguage = $subIdxLanguage;
    }

    public function handle()
    {

    }

    public function failed()
    {

    }

}
