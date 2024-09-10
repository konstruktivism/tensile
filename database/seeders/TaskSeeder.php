<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Organisation;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Task::truncate();

        Task::factory()->create(
            [
                'name' => 'Create a new version of the website',
                'description' => 'Create a new version of the website based on the Living and Jobs websites',
                'project_id' => Organisation::where('name', 'Fynders')->first()->id ?? null,
                'hours' => 4,
                'completed_at' => Carbon::now()->setDate(Carbon::now()->year, 9, 2)->format('Y-m-d'),
            ],
        );
    }
}
