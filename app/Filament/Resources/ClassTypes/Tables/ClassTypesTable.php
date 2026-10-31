<?php

namespace App\Filament\Resources\ClassTypes\Tables;

use App\Models\ClassType;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')
                    ->label('Kelas')
                    ->weight('semibold')
                    ->description(fn (ClassType $record) => $record->summary)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('intro')
                    ->label('')
                    ->state(fn (ClassType $record) => $record->is_intro ? 'Kelas pertama' : null)
                    ->badge()
                    ->color('success'),
                TextColumn::make('kind')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('level')
                    ->label('Level')
                    ->badge(),
                TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->formatStateUsing(fn (int $state) => "{$state} menit"),
                TextColumn::make('capacity')
                    ->label('Kapasitas')
                    ->formatStateUsing(fn (int $state) => "{$state} orang"),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
