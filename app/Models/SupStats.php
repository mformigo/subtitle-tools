<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SjorsO\Sup\Formats\Bluray\BluraySup;
use SjorsO\Sup\Formats\Dvd\DvdSup;
use SjorsO\Sup\Formats\Hddvd\HddvdSup;

class SupStats extends Model
{
    protected $guarded = [];

    protected $casts = [
        'bluray_sup_count' => 'int',
        'hddvd_sup_count' => 'int',
        'dvd_sup_count' => 'int',
        'total_size' => 'int',
        'images_ocrd_count' => 'int',
        'milliseconds_spent_ocring' => 'int',
    ];

    public static function today()
    {
        return static::firstOrCreate([
            'date' => now()->format('Y-m-d')
        ], [
            'bluray_sup_count' => 0,
            'hddvd_sup_count' => 0,
            'dvd_sup_count' => 0,
            'total_size' => 0,
            'images_ocrd_count' => 0,
            'milliseconds_spent_ocring' => 0,
        ]);
    }

    public static function recordNewSupFile($class, int $size)
    {
        static::today();

        switch ($class) {
            case BluraySup::class:
                $typeColumn = 'bluray_sup_count';
                break;
            case DvdSup::class:
                $typeColumn = 'dvd_sup_count';
                break;
            case HddvdSup::class:
                $typeColumn = 'hddvd_sup_count';
                break;
            default:
                throw new RuntimeException('Invalid Sup class');
        }

        DB::table('sup_stats')
            ->where('date', now()->format('Y-m-d'))
            ->update([
                $typeColumn => DB::raw("$typeColumn + 1"),
                'total_size' => DB::raw("total_size + $size"),
            ]);
    }

    public static function recordImageOcrd($msSpentOcring, $imageCount = 1)
    {
        static::ensureModelExists();

        DB::table('sup_stats')
            ->where('date', now()->format('Y-m-d'))
            ->update([
                'images_ocrd_count' => DB::raw("images_ocrd_count + $imageCount"),
                'milliseconds_spent_ocring' => DB::raw("milliseconds_spent_ocring + $msSpentOcring"),
            ]);
    }

    /**
     * To prevent a databases queries, only try creating the model when we
     * can't be sure if it exists yet.
     */
    private static function ensureModelExists()
    {
        if (in_array(now()->format('H:i'), ['00:00', '00:01', '00:02'])) {
            static::today();
        }
    }
}
