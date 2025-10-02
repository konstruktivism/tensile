#!/bin/bash

echo "🧪 Laravel Tensile Test Runner"
echo "=============================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    echo "Please run this script from the project root directory"
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction
    echo ""
fi

# Check if database is set up
echo "🗄️  Checking database setup..."
if [ ! -f "database/database.sqlite" ]; then
    echo "Creating SQLite database for testing..."
    touch database/database.sqlite
fi

# Run migrations for testing
echo "🔄 Running database migrations..."
php artisan migrate --force

echo ""
echo "🧪 Running Tests..."
echo "=================="
echo ""

# Run tests with detailed output
echo "Running all tests..."
php artisan test --verbose

echo ""
echo "📊 Test Results Summary:"
echo "========================"
echo ""

# Run tests and capture output
TEST_OUTPUT=$(php artisan test 2>&1)

# Extract test results
PASSED=$(echo "$TEST_OUTPUT" | grep -o "[0-9]* passed" | head -1)
FAILED=$(echo "$TEST_OUTPUT" | grep -o "[0-9]* failed" | head -1)

if [ -n "$PASSED" ]; then
    echo "✅ $PASSED"
fi

if [ -n "$FAILED" ]; then
    echo "❌ $FAILED"
fi

echo ""
echo "🔍 For detailed output, run:"
echo "php artisan test --verbose"
echo ""
echo "🔍 For specific test suites:"
echo "php artisan test --testsuite=Unit"
echo "php artisan test --testsuite=Feature"
echo ""
echo "🔍 For individual test files:"
echo "php artisan test tests/Feature/BasicTest.php"
echo "php artisan test tests/Unit/Models/UserTest.php"
