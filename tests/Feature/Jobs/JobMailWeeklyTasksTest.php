<?php

use App\Jobs\JobMailWeeklyTasks;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Organisation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

it('can dispatch weekly tasks email job', function () {
    Queue::fake();

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    dispatch($job);

    Queue::assertPushed(JobMailWeeklyTasks::class);
});

it('sends weekly tasks email for projects with notifications enabled', function () {
    Mail::fake();

    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'notifications' => true,
    ]);

    // Attach user to project
    $project->users()->attach($user);

    // Create tasks for current week
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek()->addDays(1),
        'minutes' => 120,
        'name' => 'Test Task 1',
    ]);

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    $job->handle();

    Mail::assertSent(\App\Mail\WeeklyTasksMail::class, function ($mail) use ($user, $project) {
        return $mail->hasTo($user->email);
    });
});

it('does not send email for projects without notifications', function () {
    Mail::fake();

    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'notifications' => false,
    ]);

    $project->users()->attach($user);

    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek()->addDays(1),
        'minutes' => 120,
    ]);

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    $job->handle();

    Mail::assertNotSent(\App\Mail\WeeklyTasksMail::class);
});

it('does not send email for projects with no tasks in the week', function () {
    Mail::fake();

    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'notifications' => true,
    ]);

    $project->users()->attach($user);

    // Create task for different week
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->subWeek(),
        'minutes' => 120,
    ]);

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    $job->handle();

    Mail::assertNotSent(\App\Mail\WeeklyTasksMail::class);
});

it('does not send email for projects with tasks but no hours logged', function () {
    Mail::fake();

    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'notifications' => true,
    ]);

    $project->users()->attach($user);

    // Create task with 0 minutes
    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek(),
        'minutes' => 0,
    ]);

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    $job->handle();

    Mail::assertNotSent(\App\Mail\WeeklyTasksMail::class);
});

it('sends email to all users assigned to the project', function () {
    Mail::fake();

    $users = User::factory()->count(3)->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'notifications' => true,
    ]);

    // Attach all users to project
    foreach ($users as $user) {
        $project->users()->attach($user);
    }

    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek()->addDays(1),
        'minutes' => 120,
    ]);

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    $job->handle();

    foreach ($users as $user) {
        Mail::assertSent(\App\Mail\WeeklyTasksMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
});

it('processes multiple week numbers', function () {
    Queue::fake();

    $weekNumbers = [now()->weekOfYear, now()->subWeek()->weekOfYear];
    $job = new JobMailWeeklyTasks($weekNumbers);

    expect($job->weekNumbers)->toBe($weekNumbers);
});

it('uses current week when no week numbers provided', function () {
    $job = new JobMailWeeklyTasks();

    expect($job->weekNumbers)->toBe([now()->weekOfYear]);
});

it('logs activity when email is sent', function () {
    $user = User::factory()->create();
    $organisation = Organisation::factory()->create();
    $project = Project::factory()->create([
        'organisation_id' => $organisation->id,
        'notifications' => true,
    ]);

    $project->users()->attach($user);

    Task::factory()->create([
        'project_id' => $project->id,
        'completed_at' => Carbon::now()->startOfWeek()->addDays(1),
        'minutes' => 120,
    ]);

    $job = new JobMailWeeklyTasks([now()->weekOfYear]);
    $job->handle();

    // Check that activity was logged
    $this->assertDatabaseHas('activity_log', [
        'subject_type' => get_class($project),
        'subject_id' => $project->id,
        'event' => 'created',
    ]);
});
