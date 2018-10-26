<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileJob;
use App\Models\StoredFileMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FileJobsController
{
    public function index(Request $request)
    {
        $encodings = StoredFileMeta::query()
            ->whereNotNull('encoding')
            ->select('encoding')
            ->groupBy('encoding')
            ->pluck('encoding')
            ->sort();

        $types = StoredFileMeta::query()
            ->whereNotNull('identified_as')
            ->select('identified_as')
            ->groupBy('identified_as')
            ->pluck('identified_as')
            ->sort();

        $filterEncoding = $request->get('encoding');

        $filterType = $request->get('type');

        $fileJobs = FileJob::query()
            ->with('inputStoredFile', 'inputStoredFile.meta')
            ->when($filterEncoding, function (Builder $query) use ($filterEncoding) {
                $query->whereHas('inputStoredFile.meta', function (Builder $query) use ($filterEncoding) {
                    $query->where('encoding', $filterEncoding);
                });
            })
            ->when($filterType, function (Builder $query) use ($filterType) {
                $query->whereHas('inputStoredFile.meta', function (Builder $query) use ($filterType) {
                    $query->where('identified_as', $filterType);
                });
            })
            ->orderByDesc('id')
            ->take(1000)
            ->get();

        return view('admin.filejobs', [
            'fileJobs' => $fileJobs,
            'encodings' => $encodings,
            'types' => $types,
        ]);
    }
}
