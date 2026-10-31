<?php

namespace App\Filament\Resources\Instructors\Tables;

use App\Models\ClassSession;
use App\Models\Instructor;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstructorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'classSessions as sessions_this_week_count' => fn (Builder $query) => $query
                    ->whereNull('cancelled_at')
                    ->whereBetween('starts_at', ClassSession::weekRange(now())),
            ]))
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->weight('semibold')
                    ->description(fn (Instructor $record) => $record->tagline)
                    ->searchable(),
                TextColumn::make('specialties')
                    ->label('Keahlian')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('sessions_this_week_count')
                    ->label('Kelas minggu ini')
                    ->numeric(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
