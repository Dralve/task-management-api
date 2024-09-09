<?php

namespace Database\Seeders\Api\V1;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'manager',
            'email' => 'manager@email.com',
            'password' =>Hash::make('12345678'),
            'role' => 'manager',
        ]);
    }
}
