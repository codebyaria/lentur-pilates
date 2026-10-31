<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingStatus;
use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Filament\Support\BookingTable;
use App\Models\Booking;
use App\Models\ClassSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->join('class_sessions', 'class_sessions.id', '=', 'bookings.class_session_id')
                ->select('bookings.*'))
            ->defaultSort('class_sessions.starts_at')
            ->columns([
                TextColumn::make('classSession.starts_at')
                    ->label('Jadwal')
                    ->formatStateUsing(fn (Booking $record) => $record->classSession->starts_at->isoFormat('ddd D MMM · HH:mm'))
                    ->description(fn (Booking $record) => $record->classSession->classType->name.' · '.$record->classSession->instructor->name)
                    ->url(fn (Booking $record) => ClassSessionResource::getUrl('view', ['record' => $record->classSession]))
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('class_sessions.starts_at', $direction === 'desc' ? 'desc' : 'asc')),
                BookingTable::memberColumn(),
                BookingTable::flagsColumn(),
                BookingTable::notesColumn(),
                BookingTable::packageColumn(),
                BookingTable::statusColumn(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Periode')
                    ->options([
                        'upcoming' => 'Mulai hari ini',
                        'today' => 'Hari ini',
                        'this_week' => 'Minggu ini',
                        'past' => 'Sudah lewat',
                    ])
                    ->default('upcoming')
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'upcoming' => $query->where('class_sessions.starts_at', '>=', today()),
                        'today' => $query->whereBetween('class_sessions.starts_at', [today(), today()->endOfDay()]),
                        'this_week' => $query->whereBetween('class_sessions.starts_at', ClassSession::weekRange(now())),
                        'past' => $query->where('class_sessions.starts_at', '<', now()),
                        default => $query,
                    }),
                Filter::make('first_class')
                    ->label(BookingTable::FIRST_CLASS)
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_first_class', true)),
                Filter::make('has_notes')
                    ->label(BookingTable::HAS_NOTES)
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereHas('member', fn (Builder $query) => $query->whereNotNull('injury_notes'))),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BookingStatus::class)
                    ->multiple()
                    ->default(array_map(fn (BookingStatus $status) => $status->value, BookingStatus::holdingSpot())),
            ])
            ->recordActions(BookingTable::recordActions());
    }
}
