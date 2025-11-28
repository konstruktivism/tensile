<?php

namespace App\Jobs;

use App\Models\ForecastTask;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCleanupForecastTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $completedTasks = Task::whereNotNull('completed_at')
            ->whereNotNull('icalUID')
            ->get();

        $deletedCount = 0;

        foreach ($completedTasks as $completedTask) {
            // Find forecast tasks with matching icalUID that haven't been soft deleted
            $forecastTasks = ForecastTask::where('icalUID', $completedTask->icalUID)
                ->whereNull('deleted_at')
                ->get();

            foreach ($forecastTasks as $forecastTask) {
                // Check if dates are close (within 7 days) to account for scheduling changes
                $completedDate = Carbon::parse($completedTask->completed_at)->startOfDay();
                $scheduledDate = Carbon::parse($forecastTask->scheduled_at)->startOfDay();
                $daysDiff = abs($completedDate->diffInDays($scheduledDate));

                if ($daysDiff <= 7) {
                    $forecastTask->delete();
                    $deletedCount++;
                }
            }
        }

        \Log::info("Forecast cleanup completed. Soft deleted {$deletedCount} forecast tasks that have been completed.");
    }
}
