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
    }
}
