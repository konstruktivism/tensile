<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Carbon\Carbon;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required(),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('completed_at')
                    ->nullable(),
                Forms\Components\TextInput::make('minutes')
                    ->label('Minutes')
                    ->nullable()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->url(fn ($record) =>  route('filament.admin.resources.projects.edit', $record->project))
                    ->getStateUsing(fn ($record) => $record->project->is_fixed ?  $record->project->name . ' (Fixed)' : $record->project->name),
                Tables\Columns\TextColumn::make('completed_at')->date()->sortable(),
                Tables\Columns\TextColumn::make('week_year')
                    ->label('Week-Year')
                    ->getStateUsing(fn ($record) => Carbon::parse($record->completed_at)->format('W-Y'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('minutes')->sortable()->formatStateUsing(fn ($state) => $state / 60)
                    ->summarize([
                        Sum::make()->formatStateUsing(fn ($state) => $state / 60),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Project'),
                Tables\Filters\SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Project'),
                Filter::make('completed_at')
                    ->form([
                        DatePicker::make('completed_at')
                            ->label(null),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['completed_at'], fn ($query, $date) => $query->whereDate('completed_at', $date));
                    })
                    ->label(null),
                Filter::make('week')
                    ->form([
                        DatePicker::make('week')
                            ->weekStartsOnMonday()
                            ->label('Select Week')
                            ->displayFormat('W')
                            ->displayFormat('Y-\WW') // Display year and week number
                            ->format('Y-\WW') // Ensure the value is stored in the correct format
                    ])
                    ->query(function ($query, array $data) {
                        if (isset($data['week'])) {
                            $startOfWeek = Carbon::parse($data['week'])->startOfWeek();
                            $endOfWeek = Carbon::parse($data['week'])->endOfWeek();
                            return $query->whereBetween('completed_at', [$startOfWeek, $endOfWeek]);
                        }
                        return $query;
                    })
                    ->label('Week'),
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(1000);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
