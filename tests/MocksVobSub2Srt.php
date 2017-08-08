<?php

namespace Tests;

use App\Subtitles\VobSub\VobSub2SrtInterface;
use App\Subtitles\VobSub\VobSub2SrtMock;

trait MocksVobSub2Srt
{
    private function useMockVobSub2Srt()
    {
        app()->bind(VobSub2SrtInterface::class, function($app, $args) {
            return new VobSub2SrtMock(
                $args['path'],
                $args['subIdx'] ?? null
            );
        });
    }
}
