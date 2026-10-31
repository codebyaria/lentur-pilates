<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Models\Package;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('memberships'))
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')
                    ->label('Paket')
                    ->weight('semibold')
                    ->description(fn (Package $record) => $record->summary)
                    ->wrap(),
                TextColumn::make('tags')
                    ->label('')
                    ->state(fn (Package $record) => array_values(array_filter([
                        $record->is_intro ? 'Kelas pertama' : null,
                        $record->is_shareable ? 'Berdua' : null,
                    ])))
                    ->badge()
                    ->color('success'),
                TextColumn::make('class_count')
                    ->label('Kelas')
                    ->numeric(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn (int $state) => Package::formatRupiah($state))
                    ->description(fn (Package $record) => Package::formatRupiah($record->pricePerClass()).' / kelas'),
                TextColumn::make('validity_days')
                    ->label('Masa berlaku')
                    ->formatStateUsing(fn (int $state) => "{$state} hari"),
                TextColumn::make('memberships_count')
                    ->label('Terjual')
                    ->numeric(),
                ToggleColumn::make('is_active')
                    ->label('Dijual'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
