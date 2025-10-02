<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;
use Carbon\Carbon;

echo "🔧 Fixing OBE Development Obeya Task...\n\n";

// Find the incorrectly imported task
$icalUID = '1eq9smf6hu5dcssb6q47i86qbn@google.com';
$task = Task::where('icalUID', $icalUID)->first();

if ($task) {
    echo "✅ Found existing task (ID: {$task->id}):\n";
    echo "- Name: {$task->name}\n";
    echo "- Current Date: {$task->completed_at->format('Y-m-d H:i:s')}\n";
    echo "- Current Minutes: {$task->minutes}\n";
    echo "- Current Week: {$task->completed_at->format('W')}\n\n";

    echo "🗑️  Deleting incorrect task...\n";
    $task->delete();
    echo "✅ Deleted task ID: {$task->id}\n\n";
} else {
    echo "ℹ️  No existing task found to delete.\n\n";
}

// Create the correct task
echo "➕ Creating new task with correct date...\n";

$newTask = Task::create([
    'name' => 'development Obeya',
    'description' => '',
    'completed_at' => '2025-09-30 09:00:00',
    'project_id' => 23,
    'icalUID' => $icalUID,
    'minutes' => 540, // 9 hours (9:00 to 18:00)
    'is_service' => false,
]);

echo "✅ Created new task:\n";
echo "- ID: {$newTask->id}\n";
echo "- Name: {$newTask->name}\n";
echo "- Date: {$newTask->completed_at->format('Y-m-d H:i:s')} ({$newTask->completed_at->format('l, F d, Y')})\n";
echo "- Minutes: {$newTask->minutes} (" . round($newTask->minutes / 60, 2) . " hours)\n";
echo "- Week: {$newTask->completed_at->format('W')} of {$newTask->completed_at->format('Y')}\n";
echo "- Project ID: {$newTask->project_id}\n\n";

echo "🔗 View at: https://tensile.test/project/23/{$newTask->completed_at->format('W')}\n\n";

echo "✅ Fix complete!\n";
