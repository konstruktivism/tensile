<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')->label('Log Name')->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Description')->sortable(),
                Tables\Columns\TextColumn::make('subject_type')->label('Subject Type')->sortable(),
                Tables\Columns\TextColumn::make('subject_id')->label('Subject ID')->sortable(),
                Tables\Columns\TextColumn::make('causer_type')->label('Causer Type')->sortable(),
                Tables\Columns\TextColumn::make('causer_id')->label('Causer ID')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('properties')
                    ->label('Properties')
                    ->getStateUsing(function ($record) {
                        $properties = $record->properties;
                        return is_array($properties) ? implode(', ', $properties) : (is_object($properties) ? json_encode($properties) : (string) $properties);
                    }),
            ])
            ->filters([
                // Define your filters here
            ])
            ->actions([
                // Define your actions here
            ])
            ->bulkActions([
                // Define your bulk actions here
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
