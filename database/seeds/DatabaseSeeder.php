<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            SubIdxTableSeeder::class,
            SupTableSeeder::class,
            StoredFilesTableSeeder::class,
            DiskUsageTableSeeder::class,
        ]);
    }
}
