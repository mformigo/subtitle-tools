<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\FileBag;

class SwapSubAndIdx
{
    public function handle($request, Closure $next)
    {
        // Swap the ".sub" and ".idx" file if the user put them in the wrong field.
        $this->maybeSwapSubAndIdx($request, $request->files);

        return $next($request);
    }

    private function maybeSwapSubAndIdx(Request $request, FileBag $fileBag)
    {
        if (! $fileBag->has('sub') || ! $fileBag->get('sub')->isValid()) {
            return;
        }

        if (! $fileBag->has('idx') || ! $fileBag->get('idx')->isValid()) {
            return;
        }

        $idxExtension = $fileBag->get('idx')->getClientOriginalExtension();

        if (strtolower($idxExtension) === 'sub') {
            $request->files->replace([
                'sub' => $fileBag->get('idx'),
                'idx' => $fileBag->get('sub'),
            ]);
        }
    }
}
