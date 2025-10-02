<?php

require_once 'vendor/autoload.php';

echo "Testing basic setup...\n";

try {
    // Test if models can be loaded
    $user = new App\Models\User();
    echo "✓ User model loaded\n";

    $project = new App\Models\Project();
    echo "✓ Project model loaded\n";

    $task = new App\Models\Task();
    echo "✓ Task model loaded\n";

    $organisation = new App\Models\Organisation();
    echo "✓ Organisation model loaded\n";

    echo "\nAll models loaded successfully!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
