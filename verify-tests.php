<?php

echo "🧪 Laravel Tensile Test Verification\n";
echo "===================================\n\n";

// Check if we're in Laravel project
if (!file_exists('artisan')) {
    echo "❌ Error: Not in Laravel project directory\n";
    exit(1);
}

echo "✅ Laravel project detected\n";

// Check if vendor exists
if (!is_dir('vendor')) {
    echo "❌ Error: Vendor directory not found. Run 'composer install' first\n";
    exit(1);
}

echo "✅ Composer dependencies found\n";

// Check if test files exist
$testFiles = [
    'tests/Feature/BasicTest.php',
    'tests/Unit/Models/UserTest.php',
    'tests/Unit/Models/ProjectTest.php',
    'tests/Unit/Models/TaskTest.php',
    'tests/Unit/Models/OrganisationTest.php',
    'tests/Feature/ProjectControllerTest.php',
    'tests/Feature/StatsControllerTest.php',
    'tests/Feature/ProfileControllerTest.php',
    'tests/Feature/GoogleCalendarControllerTest.php',
    'tests/Feature/MoneybirdControllerTest.php',
    'tests/Feature/Auth/AuthenticatedSessionControllerTest.php',
    'tests/Feature/Auth/RegisteredUserControllerTest.php',
    'tests/Feature/Auth/MagicLinkControllerTest.php',
    'tests/Feature/Jobs/JobMailWeeklyTasksTest.php',
];

echo "📁 Checking test files:\n";
foreach ($testFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file\n";
    } else {
        echo "❌ $file (missing)\n";
    }
}

echo "\n📋 Test Configuration:\n";
if (file_exists('phpunit.xml')) {
    echo "✅ phpunit.xml exists\n";
} else {
    echo "❌ phpunit.xml missing\n";
}

if (file_exists('tests/Pest.php')) {
    echo "✅ Pest configuration exists\n";
} else {
    echo "❌ Pest configuration missing\n";
}

if (file_exists('tests/TestCase.php')) {
    echo "✅ TestCase.php exists\n";
} else {
    echo "❌ TestCase.php missing\n";
}

echo "\n🚀 Ready to run tests!\n";
echo "======================\n";
echo "Run: php artisan test\n";
echo "Or: ./run-tests-manual.sh\n";
