<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;

echo "🔍 Checking for missing September 30th task...\n\n";

// Check tasks around September 30th
$startDate = Carbon::parse('2024-09-25');
$endDate = Carbon::parse('2024-10-05');

echo "📅 Looking for tasks between {$startDate->format('Y-m-d')} and {$endDate->format('Y-m-d')}...\n\n";

$tasks = Task::whereBetween('completed_at', [$startDate, $endDate])
    ->orderBy('completed_at')
    ->get(['name', 'completed_at', 'minutes', 'icalUID', 'project_id']);

if ($tasks->count() > 0) {
    echo "✅ Found {$tasks->count()} tasks in this period:\n";
    echo str_repeat("-", 80) . "\n";

    foreach ($tasks as $task) {
        $project = Project::find($task->project_id);
        echo sprintf(
            "%s | %s | %d min | %s | %s\n",
            $task->completed_at->format('Y-m-d H:i'),
            $task->name,
            $task->minutes,
            $project ? $project->name : 'Unknown Project',
            $task->icalUID
        );
    }
} else {
    echo "❌ No tasks found in this period.\n";
}

echo "\n" . str_repeat("=", 80) . "\n";

// Check specifically for September 30th
$sept30 = Carbon::parse('2024-09-30');
echo "🎯 Looking specifically for September 30th tasks...\n";

$sept30Tasks = Task::whereDate('completed_at', $sept30)->get();

if ($sept30Tasks->count() > 0) {
    echo "✅ Found {$sept30Tasks->count()} tasks on September 30th:\n";
    foreach ($sept30Tasks as $task) {
        echo "- {$task->completed_at->format('H:i')} - {$task->name} ({$task->minutes} min)\n";
    }
} else {
    echo "❌ No tasks found on September 30th, 2024.\n";
}

echo "\n" . str_repeat("=", 80) . "\n";

// Check for 9-hour tasks (9:00 to 18:00 = 9 hours = 540 minutes)
echo "🕘 Looking for tasks with ~540 minutes (9 hours)...\n";

$longTasks = Task::whereBetween('minutes', [500, 600])->get();

if ($longTasks->count() > 0) {
    echo "✅ Found {$longTasks->count()} tasks with ~9 hours duration:\n";
    foreach ($longTasks as $task) {
        echo "- {$task->completed_at->format('Y-m-d H:i')} - {$task->name} ({$task->minutes} min)\n";
    }
} else {
    echo "❌ No tasks found with ~9 hours duration.\n";
}

echo "\n" . str_repeat("=", 80) . "\n";

// Check import logs
echo "📋 Recent import activity...\n";

$recentLogs = \App\Models\ActivityLog::where('log_name', 'task')
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

if ($recentLogs->count() > 0) {
    echo "✅ Recent task imports:\n";
    foreach ($recentLogs as $log) {
        echo "- {$log->created_at->format('Y-m-d H:i')} - {$log->description}\n";
    }
} else {
    echo "❌ No recent task import activity found.\n";
}

echo "\n🔍 Diagnosis complete!\n";
