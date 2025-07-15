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
use App\Mail\MonthlyTasksMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\MoneybirdController;


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
                Forms\Components\TextInput::make('hour_tariff')
                    ->label('Hour Tariff')
                    ->nullable()
                    ->numeric(),
                Forms\Components\Toggle::make('is_fixed')
                    ->default(false),
                Forms\Components\Toggle::make('is_internal')
                    ->default(false),
                Forms\Components\Toggle::make('notifications')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('organisation.name')->label('Organisation')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('project_code')->badge(),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('hour_tariff'),
                Tables\Columns\IconColumn::make('is_fixed')->boolean()->label('Fixed'),
                Tables\Columns\IconColumn::make('notifications')->boolean()->label('Notify'),
                Tables\Columns\TextColumn::make('users.name')->label('Users'),
            ])
            ->filters([
                // Add any table filters here if needed
            ])
            ->actions([
                Action::make('sendWeeklyTasks')
                    ->label('Weekly')
                    ->action(function (Project $record) {
                        $week = now()->weekOfYear;

                        $startOfWeek = Carbon::now()->setISODate(Carbon::now()->year, $week)->startOfWeek();
                        $endOfWeek = $startOfWeek->copy()->endOfWeek();

                        $tasks = $record->tasks()->whereBetween('completed_at', [$startOfWeek, $endOfWeek])->orderBy('completed_at')->get();

                        $users = $record->users;

                        foreach ($users as $user) {
                           Mail::to($user->email)->send(new WeeklyTasksMail($record, $tasks, $week));
                        }

                        activity()
                            ->performedOn($record)
                            ->log('Weekly tasks email sent for project: ' . $record->id . ' for week: ' . $week . ' to users: ' . $users->pluck('email')->implode(', '));
                    })
                    ->icon('heroicon-s-envelope'),
                Action::make('sendMonthlyTasks')
                    ->label('Monthly')
                    ->action(function (Project $record) {
                        $month = now()->subMonth()->month;

                        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
                        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

                        $tasks = $record->tasks()->whereBetween('completed_at', [$startOfMonth, $endOfMonth])->orderBy('completed_at')->get();

                        $users = $record->users;

                        foreach ($users as $user) {
                            Mail::to($user->email)->send(new MonthlyTasksMail($record, $tasks, $month));
                        }

                        activity()
                            ->performedOn($record)
                            ->log('Monthly tasks email sent for project: ' . $record->id . ' for month: ' . $month . ' to users: ' . $users->pluck('email')->implode(', '));
                    })
                    ->icon('heroicon-s-envelope'),
                Action::make('updateInvoice')
                    ->label('Update Invoice')
                    ->action(function ($record) {
                        $controller = new MoneybirdController();
                        $controller->updateInvoice($record->id);
                    })
                    ->color('primary'),
            ])
            ->defaultPaginationPageOption(100);

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
