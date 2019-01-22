<?php

use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Database\Seeder;

class SubIdxTableSeeder extends Seeder
{
    public function run()
    {
        factory(SubIdx::class)
            ->create(['url_key' => '1'])
            ->languages()->saveMany(collect([
                factory(SubIdxLanguage::class)->state('idle')->make(),
                factory(SubIdxLanguage::class)->state('queued')->make(),
                factory(SubIdxLanguage::class)->state('queued')->make(),
                factory(SubIdxLanguage::class)->state('processing')->make(),
                factory(SubIdxLanguage::class)->state('failed')->make(),
                factory(SubIdxLanguage::class)->state('finished')->make(),
            ])->shuffle());

        factory(SubIdx::class)
            ->create(['url_key' => '2'])
            ->languages()->save(
                factory(SubIdxLanguage::class)->state('finished')->make()
            );

        factory(SubIdx::class)
            ->create(['url_key' => '3'])
            ->languages()->saveMany(
                factory(SubIdxLanguage::class, 3)->state('idle')->make()
            );

        factory(SubIdx::class)
            ->create(['url_key' => '4'])
            ->languages()->saveMany(
                factory(SubIdxLanguage::class, 3)->state('finished')->make()
            );

        factory(SubIdx::class)
            ->create(['url_key' => '5'])
            ->languages()->saveMany(
                factory(SubIdxLanguage::class, 16)->state('idle')->make()
            );


        factory(SubIdx::class)->create(['original_name' => 'smallfoot.2018.1080p.bluray.x264-drones', 'cache_hits' => 67]);
        factory(SubIdx::class)->create(['original_name' => 'Fantastic Beasts And Where To Find Them 2016 BRRip x264 1080p-NPW', 'cache_hits' => 48]);
        factory(SubIdx::class)->create(['original_name' => 'The.House.with.a.Clock.in.Its.Walls.2018.BRRip.XviD.AC3-XVID', 'cache_hits' => 32]);
        factory(SubIdx::class)->create(['original_name' => 'Bobby.Robson.More.than.a.Manager.2018.1080p.BluRay.H264.AAC-RARBG', 'cache_hits' => 30]);
        factory(SubIdx::class)->create(['original_name' => 'a.x.l.2018.1080p.bluray.x264-geckos', 'cache_hits' => 29]);
    }
}
