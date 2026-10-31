<?php

namespace App\Filament\Resources\Members\Tables;

use App\Filament\Support\BookingTable;
use App\Models\Member;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nickname')
            ->columns([
                TextColumn::make('nickname')
                    ->label('Nama panggilan')
                    ->weight('semibold')
                    ->description(fn (Member $record) => $record->whatsapp)
                    ->searchable(['nickname', 'whatsapp'])
                    ->sortable(),
                TextColumn::make('flags')
                    ->label('Tanda')
                    ->state(fn (Member $record) => array_values(array_filter([
                        $record->is_new_to_pilates ? 'Baru pilates' : null,
                        $record->hasInjuryNotes() ? BookingTable::HAS_NOTES : null,
                    ])))
                    ->badge()
                    ->color(fn (string $state) => $state === 'Baru pilates' ? 'success' : 'warning')
                    ->tooltip(fn (Member $record) => $record->injury_notes)
                    ->placeholder('—'),
                TextColumn::make('currentMembership.package.name')
                    ->label('Paket aktif')
                    ->description(fn (Member $record) => $record->currentMembership
                        ? 'berlaku s.d. '.$record->currentMembership->expires_at->isoFormat('D MMM')
                        : null)
                    ->placeholder('Tidak ada'),
                TextColumn::make('currentMembership.classes_remaining')
                    ->label('Sisa kelas')
                    ->formatStateUsing(fn (Member $record) => $record->currentMembership
                        ? "{$record->currentMembership->classes_remaining} / {$record->currentMembership->classes_total}"
                        : null)
                    ->badge()
                    ->color(fn (Member $record) => ($record->currentMembership->classes_remaining ?? 0) <= 1 ? 'warning' : 'success')
                    ->placeholder('—'),
                TextColumn::make('classes_taken_count')
                    ->label('Kelas diikuti')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('referredBy.nickname')
                    ->label('Diajak oleh')
                    ->icon(Heroicon::OutlinedHeart)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_notes')
                    ->label(BookingTable::HAS_NOTES)
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereNotNull('injury_notes')),
                Filter::make('new_to_pilates')
                    ->label('Baru pilates')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_new_to_pilates', true)),
                Filter::make('without_package')
                    ->label('Tanpa paket aktif')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereDoesntHave('currentMembership')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
