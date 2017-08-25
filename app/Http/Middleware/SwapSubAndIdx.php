<?php

namespace App\Http\Middleware;

use Closure;

class SwapSubAndIdx
{
    public function handle($request, Closure $next)
    {
        // If the user put the sub and idx file in the wrong field, swap them

        $fileBag = $request->files;

        if($fileBag->has('sub') && $fileBag->has('idx') && $fileBag->get('sub')->isValid() && $fileBag->get('idx')->isValid()) {
            $shouldSwap = strtolower($fileBag->get('idx')->getClientOriginalExtension()) === "sub";

            if($shouldSwap) {
                $request->files->replace([
                    'sub' => $fileBag->get('idx'),
                    'idx' => $fileBag->get('sub'),
                ]);
            }
        }

        return $next($request);
    }

}
