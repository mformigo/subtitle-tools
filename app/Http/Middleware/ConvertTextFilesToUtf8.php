<?php

namespace App\Http\Middleware;

use App\Utils\TextFileIdentifier;
use App\Utils\TextFileReader;
use Closure;

class ConvertTextFilesToUtf8
{
    public function handle($request, Closure $next, ...$names)
    {
        $fileBag = $request->files;
        $textFileIdentifier = app(TextFileIdentifier::class);
        $textFileReader = app(TextFileReader::class);

        foreach($names as $name) {
            if(!$fileBag->has($name) || !$fileBag->get($name)->isValid()) {
                continue;
            }

            $filePath = $fileBag->get($name)->getRealPath();

            $utf8Content = $textFileReader->getContents($filePath);

            file_put_contents($filePath, $utf8Content);
        }

        return $next($request);
    }

}
