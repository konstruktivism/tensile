#!/bin/bash

echo "Running Laravel tests with Pest..."

# Install dependencies if needed
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction
fi

if [ ! -d "node_modules" ]; then
    echo "Installing npm dependencies..."
    npm install --no-audit
fi

# Run tests
echo "Running tests..."
php artisan test

echo "Tests completed!"
