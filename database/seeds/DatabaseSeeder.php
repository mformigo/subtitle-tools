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
        ]);

        file_put_contents(
            storage_path('logs/disk-usage.txt'),
            '{"size":"30gb","used":"11gb","available":"19gb","percentage":"36%","warning":false,"error":null}'
        );
    }
}
