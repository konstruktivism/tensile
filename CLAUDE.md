# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Tensile is a Laravel-based project management application that helps users track time, manage projects, and generate reports. It uses Filament for the admin panel and integrates with Google Calendar and Moneybird for data synchronization.

## Development Commands

### PHP/Laravel Commands
- `composer install` - Install PHP dependencies
- `php artisan serve` - Start local development server
- `php artisan migrate` - Run database migrations
- `php artisan key:generate` - Generate application key
- `php artisan tinker` - Start interactive PHP shell
- `php artisan vendor:publish --tag=laravel-assets --ansi --force` - Publish Laravel assets
- `php artisan filament:upgrade` - Upgrade Filament components

### Frontend Commands
- `npm install` - Install Node.js dependencies
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build production assets

### Testing
- `./vendor/bin/pest` - Run tests with Pest framework
- `./vendor/bin/phpunit` - Run tests with PHPUnit

### Code Quality
- `./vendor/bin/pint` - Run Laravel Pint code formatter

## Architecture

### Core Models
- **User**: Authenticated users with organisation relationships and Filament admin access
- **Organisation**: Top-level entity for grouping projects and users
- **Project**: Individual projects with tasks, users, and billing information
- **Task**: Time entries with Google Calendar integration and invoicing status

### Key Controllers
- **ProjectController**: Handles project views and week-based task filtering
- **StatsController**: Provides API endpoints for dashboard widgets (hours/revenue per week)
- **GoogleCalendarController**: Manages Google Calendar event import
- **MoneybirdController**: Handles Moneybird invoice integration
- **MagicLinkController**: Implements passwordless authentication

### Filament Admin Panel
- Admin panel accessible at `/admin` for users with @konstruktiv.nl email addresses
- Resources for managing Users, Organisations, Projects, Tasks, and Activities
- Custom widgets for hours tracking and import functionality
- Located in `app/Filament/` directory

### Console Commands
- `DailyTask` - Import tasks from Google Calendar (runs at 6:00 AM daily and 23:54 on Sundays)
- `SendWeeklyTasks` - Send weekly task summaries via email (Sundays at 23:59)
- `SendMonthlyTasks` - Send monthly task summaries (first Monday of each month at 6:00 AM)

### External Integrations
- **Google Calendar**: Automatic task import using Google Calendar API
- **Moneybird**: Invoice management and synchronization
- **Mailcoach**: Email notifications for task summaries

### Database Structure
- Uses SQLite for development (`database/database.sqlite`)
- Migrations handle Users, Projects, Organisations, Tasks, and activity logging
- Many-to-many relationship between Users and Projects
- Tasks include Google Calendar UID tracking and invoicing status

### Frontend Stack
- **Vite** for asset compilation
- **Tailwind CSS** for styling
- **Alpine.js** for interactive components
- **Blade templates** for server-side rendering
- **MJML** for email template compilation

## Environment Configuration

Required environment variables:
- `GOOGLE_CALENDAR_AUTH_CODE` - Google Calendar authentication
- `GOOGLE_CALENDAR_ID` - Google Calendar ID
- `MONEYBIRD_TOKEN` - Moneybird API token
- `MONEYBIRD_ADMINISTRATION_ID` - Moneybird administration ID
- `MONEYBIRD_LEDGER_ACCOUNT_ID` - Moneybird ledger account ID
- `MAILCOACH_TOKEN` - Mailcoach API token

## Important Notes

- Admin panel access is restricted to users with @konstruktiv.nl email addresses
- Magic link authentication is available for passwordless login
- Task import from Google Calendar runs automatically via scheduled commands
- Activity logging is enabled for all Task model changes using Spatie\Activitylog
- Project notifications and invoicing status are tracked per project