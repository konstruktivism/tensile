<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Mail\WeeklyTasksMail;
use Illuminate\Support\Facades\Mail;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('project_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->nullable(),
                Forms\Components\Select::make('organisation_id')
                    ->relationship('organisation', 'name')
                    ->required(),
                Forms\Components\Select::make('users')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->required(),
                Forms\Components\Toggle::make('is_fixed')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('organisation.name')->label('Organisation')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('users.name')->label('Users'),
            ])
            ->filters([
                // Add any table filters here if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('sendWeeklyTasks')
                    ->label('Send Weekly Tasks')
                    ->action(function (Project $record) {
                        $week = now()->weekOfYear;

                        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek();
                        $endOfWeek = $startOfWeek->copy()->endOfWeek();

                        $tasks = $record->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])->get();

                        $users = $record->users;

                        foreach ($users as $user) {
                           Mail::to($user->email)->send(new WeeklyTasksMail($record, $tasks, $week));
                        }
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
