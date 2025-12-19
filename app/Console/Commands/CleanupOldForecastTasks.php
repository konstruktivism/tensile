<?php

namespace App\Console\Commands;

use App\Models\ForecastTask;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupOldForecastTasks extends Command
{
    protected $signature = 'forecast:cleanup-old';

    protected $description = 'Soft delete all forecast tasks scheduled before today';

    public function handle(): int
    {
        $today = Carbon::now()->startOfDay();

        $deletedCount = ForecastTask::where('scheduled_at', '<', $today)
            ->whereNull('deleted_at')
            ->count();

        if ($deletedCount === 0) {
            $this->info('No old forecast tasks to clean up.');

            return Command::SUCCESS;
        }

        ForecastTask::where('scheduled_at', '<', $today)
            ->whereNull('deleted_at')
            ->delete();

        $this->info("Successfully soft deleted {$deletedCount} old forecast task(s).");

        return Command::SUCCESS;
    }
}
