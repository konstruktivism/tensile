<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the users table
        User::truncate();

        User::factory()->create([
            'name' => 'Sander',
            'email' => 'sander@konstruktiv.nl',
            'password' => Hash::make('konstruktiv'),
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'Merijn',
            'email' => 'merijn@clienta.com',
            'password' => Hash::make('clienta'),
            'is_admin' => false,
        ]);
    }
}
