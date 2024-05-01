<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'     => 'Steve Montambeault',
            'username' => 'stevemo',
            'email'    => 'steve@stevemo.ca',
            'password' => Hash::make('secret'),
        ]);
    }
}
