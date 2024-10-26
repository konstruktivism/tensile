<?php

namespace App\Jobs;

use App\Mail\WeeklyTasksMail;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class JobMailWeeklyTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $weekNumbers;

    /**
     * Create a new job instance.
     *
     * @param array|null $weekNumbers
     */
    public function __construct(array $weekNumbers = null)
    {
        $this->weekNumbers = $weekNumbers ?? [now()->weekOfYear];
    }

    public function handle()
    {
        foreach ($this->weekNumbers as $weekNumber) {
            dump('Sending weekly tasks email for week: ' . $weekNumber);
            $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, (int) $weekNumber)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();

            $projects = Project::where('notifications', true)->get();

            foreach ($projects as $project) {
                $tasks = $project->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])->orderBy('completed_at')->get();

                if ($tasks->count() === 0) {
                    continue;
                }

                $users = $project->users;

                foreach ($users as $user) {
                    Mail::to($user->email)->send(new WeeklyTasksMail($project, $tasks, $weekNumber));
                }

                activity()
                    ->performedOn($project)
                    ->log('Weekly tasks email sent for project: ' . $project->id . ' for week: ' . $weekNumber . ' to users: ' . $users->pluck('email')->implode(', '));
            }
        }
    }
}
