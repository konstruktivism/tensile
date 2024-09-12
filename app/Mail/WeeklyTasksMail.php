<?php

// app/Mail/WeeklyTasksMail.php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyTasksMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $tasks;
    public $week;

    public function __construct(Project $project, $tasks, $week)
    {
        $this->project = $project;
        $this->tasks = $tasks;
        $this->week = $week;
    }

    public function build()
    {
        return $this->view('emails.weekly-tasks')
            ->subject('Weekly Tasks for ' . $this->project->name)
            ->with([
                'project' => $this->project,
                'tasks' => $this->tasks,
                'week' => $this->week,
            ]);
    }
}
