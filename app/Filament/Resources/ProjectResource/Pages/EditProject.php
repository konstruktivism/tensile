<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Mail\MonthlyTasksMail;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Mail\WeeklyTasksMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('sendWeeklyTasks')
                ->label('Send Weekly Tasks')
                ->action(function () {
                    $record = $this->record;
                    $week = now()->weekOfYear;

                    $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek();
                    $endOfWeek = $startOfWeek->copy()->endOfWeek();

                    $tasks = $record->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])->get();

                    $users = $record->users;

                    foreach ($users as $user) {
                        Mail::to($user->email)->send(new WeeklyTasksMail($record, $tasks, $week));
                    }
                }),
            Actions\Action::make('sendMonthlyTasks')
                ->label('Send Monthly Tasks')
                ->action(function () {
                    $record = $this->record;
                    $month = now()->month;

                    $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
                    $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

                    $tasks = $record->tasks()->whereBetween('completed_at', [$startOfMonth, $endOfMonth])->get();

                    $users = $record->users;

                    foreach ($users as $user) {
                        Mail::to($user->email)->send(new MonthlyTasksMail($record, $tasks, $month));
                    }
                }),
        ];
    }
}
