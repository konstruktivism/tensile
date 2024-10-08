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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $week = now()->weekOfYear;
        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $projects = Project::where('notifications', true)->get();

        foreach ($projects as $project) {
            $tasks = $project->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])->orderBy('completed_at')->get();
            $users = $project->users;

            foreach ($users as $user) {
                Mail::to($user->email)->send(new WeeklyTasksMail($project, $tasks, $week));
            }

            activity()
                ->performedOn($project)
                ->log('Weekly tasks email sent for project: ' . $project->id . ' for week: ' . $week . ' to users: ' . $users->pluck('email')->implode(', '));
        }
    }
}
