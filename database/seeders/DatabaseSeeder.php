<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Api\V1\AdminSeeder;
use Database\Seeders\Api\V1\ManagerSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call(AdminSeeder::class);
         $this->call(ManagerSeeder::class);
    }
}
