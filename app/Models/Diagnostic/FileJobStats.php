<?php

namespace App\Models\Diagnostic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FileJobStats extends Model
{
    protected $guarded = [];

    protected $casts = [
        'times_used'    => 'integer',
        'total_files'   => 'integer',
        'amount_failed' => 'integer',
        'total_size'    => 'integer',
    ];

    public static function collectRange($fromDate, $untilDate)
    {
        $tools = [];

        $stats = static::query()
            ->where('date', '>=', $fromDate)
            ->where('date', '<', $untilDate)
            ->get();

        $stats->unique('tool_route')->each(function (FileJobStats $stat) use (&$tools) {
            $tools[$stat->tool_route]['times_used']    = 0;
            $tools[$stat->tool_route]['total_files']   = 0;
            $tools[$stat->tool_route]['amount_failed'] = 0;
            $tools[$stat->tool_route]['total_size']    = 0;
        });

        $stats->each(function (FileJobStats $stat) use (&$tools) {
            $tools[$stat->tool_route]['times_used']    += $stat->times_used;
            $tools[$stat->tool_route]['total_files']   += $stat->total_files;
            $tools[$stat->tool_route]['amount_failed'] += $stat->amount_failed;
            $tools[$stat->tool_route]['total_size']    += $stat->total_size;
        });

        ksort($tools);

        // make sure the "*" array item is the last one
        return array_reverse($tools, true);
    }

    public static function yesterday()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $today     = Carbon::today()->toDateString();

        return static::collectRange($yesterday, $today);
    }

    public static function lastMonth()
    {
        $startOfLastMonth = Carbon::today()->startOfMonth()->subDay(1)->startOfMonth()->toDateString();
        $startOfThisMonth = Carbon::today()->startOfMonth()->toDateString();

        return static::collectRange($startOfLastMonth, $startOfThisMonth);
    }

    public static function allTime()
    {
        $today = Carbon::today()->toDateString();

        return static::collectRange('2000-01-01', $today);
    }
}
