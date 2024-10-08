<?php

namespace App\Jobs;

use App\Mail\MonthlyTasksMail;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class JobMailMonthlyTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $month = now()->subMonth()->month;
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        $projects = Project::where('notifications', true)->get();

        foreach ($projects as $project) {
            $tasks = $project->tasks()->whereBetween('completed_at', [$startOfMonth, $endOfMonth])->orderBy('completed_at')->get();
            $users = $project->users;

            foreach ($users as $user) {
                Mail::to($user->email)->send(new MonthlyTasksMail($project, $tasks, $month));
            }

            activity()
                ->performedOn($project)
                ->log('Monthly tasks email sent for project: ' . $project->id . ' for  ' . $month . ' to users: ' . $users->pluck('email')->implode(', '));
        }
    }
}
