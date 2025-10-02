<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\TestResponse;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running Laravel Tests...\n\n";

// Run a simple test to verify setup
try {
    // Test database connection
    DB::connection()->getPdo();
    echo "✓ Database connection successful\n";

    // Test model loading
    $user = new App\Models\User();
    echo "✓ User model loaded\n";

    $project = new App\Models\Project();
    echo "✓ Project model loaded\n";

    // Test factory
    $user = App\Models\User::factory()->make();
    echo "✓ User factory working\n";

    $project = App\Models\Project::factory()->make();
    echo "✓ Project factory working\n";

    echo "\n✅ Basic setup verification successful!\n";
    echo "You can now run: php artisan test\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
