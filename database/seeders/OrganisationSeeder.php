<?php

namespace Database\Seeders;
use App\Models\Organisation;
use Illuminate\Database\Seeder;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the users table
        Organisation::truncate();

        Organisation::factory()->create([
            'name' => 'Fynders',
        ]);
    }
}
