<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('command:daily-task')->dailyAt('06:00');

Schedule::command('send:weekly-tasks')->fridays()->at('06:00')->environments(['production']);

Schedule::command('send:monthly-tasks')->monthlyOn(1, '06:00')->environments(['production']);
