<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->selectRaw('projects.*, (
                SELECT MAX(completed_at)
                FROM tasks
                WHERE tasks.project_id = projects.id
            ) as latest_task_date')
            ->orderBy('latest_task_date', 'desc')
            ->orderBy('projects.id', 'desc');
    }
}
