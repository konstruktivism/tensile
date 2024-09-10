<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Organisation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the projects table
        Project::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Project::factory()->create(
            [
                'name' => 'V2',
                'description' => 'A new version based on Living and Jobs',
                'organisation_id' => Organisation::where('name', 'Fynders')->first()->id ?? null,
            ]
        );

    }
}
