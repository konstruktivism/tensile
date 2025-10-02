# Laravel Tensile - Test Suite

This test suite uses **Pest PHP v2.0** and covers all controllers, models, and jobs in the application.

## Test Structure

### Feature Tests
- **Controllers**: Tests for all HTTP controllers including authentication, projects, stats, etc.
- **Jobs**: Tests for background jobs like weekly email notifications
- **Integration**: End-to-end functionality tests

### Unit Tests
- **Models**: Tests for Eloquent models and their relationships
- **Business Logic**: Isolated unit tests for specific functionality

## Test Configuration

### Database
- Uses **SQLite in-memory database** for testing (configured in `phpunit.xml`)
- **No RefreshDatabase** - tests use a separate test database
- All database operations are isolated from your main database

### Key Features
- **Pest PHP** syntax for readable, expressive tests
- **Factory support** for creating test data
- **Mail/Queue faking** for testing email and job functionality
- **Authentication testing** with user factories

## Running Tests

### Using Laravel Artisan
```bash
php artisan test
```

### Using Pest directly
```bash
./vendor/bin/pest
```

### Using the test runner script
```bash
./run-tests.sh
```

### Running specific test files
```bash
php artisan test tests/Feature/ProjectControllerTest.php
./vendor/bin/pest tests/Unit/Models/UserTest.php
```

## Test Coverage

### Controllers Tested
- ✅ **ProjectController** - CRUD operations, authentication
- ✅ **StatsController** - Weekly/monthly stats, data aggregation
- ✅ **ProfileController** - User profile management, password updates
- ✅ **GoogleCalendarController** - Calendar import functionality
- ✅ **MoneybirdController** - API integration tests
- ✅ **Auth Controllers** - Login, registration, password reset, magic links

### Models Tested
- ✅ **User** - Relationships, magic link functionality
- ✅ **Project** - Organisation relationship, task aggregation
- ✅ **Task** - Project relationship, time calculations
- ✅ **Organisation** - Project relationships

### Jobs Tested
- ✅ **JobMailWeeklyTasks** - Email scheduling, project filtering

## Test Data

All tests use **Laravel Factories** to create realistic test data:
- `User::factory()->create()`
- `Project::factory()->create()`
- `Task::factory()->create()`
- `Organisation::factory()->create()`

## Key Test Patterns

### Authentication Testing
```php
it('requires authentication', function () {
    $response = $this->get('/protected-route');
    $response->assertRedirect('/login');
});
```

### Database Testing
```php
it('creates record in database', function () {
    $data = ['name' => 'Test'];
    $this->post('/create', $data);
    
    $this->assertDatabaseHas('table', $data);
});
```

### Mail Testing
```php
it('sends email', function () {
    Mail::fake();
    
    // Trigger email sending
    
    Mail::assertSent(MyMail::class);
});
```

## Environment

Tests run in the `testing` environment with:
- In-memory SQLite database
- Array cache and sessions
- Faked mail and queues
- Optimized for speed and isolation

## Adding New Tests

1. Create test files in appropriate directories (`tests/Feature/` or `tests/Unit/`)
2. Use Pest syntax: `it('description', function () { ... })`
3. Use factories for test data
4. Follow the existing patterns for authentication, database, and mail testing

## Notes

- Tests are designed to be **fast and isolated**
- **No database refresh** - uses separate test database
- **Realistic test data** using factories
- **Comprehensive coverage** of all major functionality
- **Pest PHP v2.0** for modern, readable test syntax
