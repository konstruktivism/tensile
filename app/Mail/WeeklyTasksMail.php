<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mjml\Exceptions\CouldNotConvertMjml;
use Spatie\Mjml\Mjml;

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


    /**
     * @throws CouldNotConvertMjml
     */
    public function build()
    {
        // Read the MJML template content
        $mjmlContent = view('mail.weekly-tasks', [
            'project' => $this->project,
            'tasks' => $this->tasks,
            'week' => $this->week,
        ])->render();

        $htmlContent = Mjml::new()->convert($mjmlContent)->html();

        return $this->view('mail.raw', ['htmlContent' => $htmlContent])
            ->subject(config('app.name') . ' · ' . 'Work Log Week ' . $this->week . ' for ' . $this->project->name . ' (' . $this->project->organisation->name . ')');
    }
}
