<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\Mjml\Exceptions\CouldNotConvertMjml;
use Spatie\Mjml\Mjml;

class MonthlyTasksMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $tasks;
    public $month;

    public function __construct(Project $project, $tasks, $month)
    {
        $this->project = $project;
        $this->tasks = $tasks;
        $this->month = $month;
    }


    /**
     * @throws CouldNotConvertMjml
     */
    public function build()
    {
        // Read the MJML template content
        $mjmlContent = view('mail.monthly-tasks', [
            'project' => $this->project,
            'tasks' => $this->tasks,
            'month' => $this->month,
        ])->render();

        $htmlContent = Mjml::new()->convert($mjmlContent)->html();

        return $this->view('mail.raw', ['htmlContent' => $htmlContent])
            ->subject('🪢 '. config('app.client') . ' · ' . 'Work Log ' . \Carbon\Carbon::now()->subMonth()->locale('en')->translatedFormat('F') . ' ' . $this->project->organisation->name);
    }
}
