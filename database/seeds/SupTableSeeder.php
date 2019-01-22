<?php

use App\Models\SupJob;
use Illuminate\Database\Seeder;

class SupTableSeeder extends Seeder
{
    public function run()
    {
        factory(SupJob::class, 10)->create();

        factory(SupJob::class)->create(['cache_hits' => 71, 'original_name' => 'Johnny.English.Strikes.Again.2018.1080p.BluRay.x264.DTS-FGT.sup']);
        factory(SupJob::class)->create(['cache_hits' => 55, 'original_name' => 'Hell.Fest.2018.1080p.BluRay.x264-Replica.sup']);
        factory(SupJob::class)->create(['cache_hits' => 38, 'original_name' => 'Unbroken.Path.to.Redemption.2018.720p.BluRay.x264-GECKOS-CHS+ENG.sup']);
        factory(SupJob::class)->create(['cache_hits' => 30, 'original_name' => '[Anime Land] Pokemon Movie 21 - Minna no Monogatari (BDRip 720p Hi444PP QAAC) [5DF969CC]_track3_[eng].sup']);
        factory(SupJob::class)->create(['cache_hits' => 11, 'original_name' => 'cht.Dunkirk敦克爾克大行動.2017.1080p.sup']);
    }
}
