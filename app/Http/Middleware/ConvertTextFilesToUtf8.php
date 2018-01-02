<?php

namespace App\Http\Middleware;

use Closure;

class ConvertTextFilesToUtf8
{
    public function handle($request, Closure $next, ...$names)
    {
        $fileBag = $request->files;
        $textFileIdentifier = app(\SjorsO\TextFile\Contracts\TextFileIdentifierInterface::class);
        $textFileReader = app(\SjorsO\TextFile\Contracts\TextFileReaderInterface::class);

        foreach ($names as $name) {
            if (!$fileBag->has($name) || !$fileBag->get($name)->isValid()) {
                continue;
            }

            $filePath = $fileBag->get($name)->getRealPath();

            $utf8Content = $textFileReader->getContent($filePath);

            file_put_contents($filePath, $utf8Content);
        }

        return $next($request);
    }

}
