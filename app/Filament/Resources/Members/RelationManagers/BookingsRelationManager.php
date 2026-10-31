<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Filament\Support\BookingTable;
use App\Models\Booking;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'Riwayat kelas';

    protected static ?string $modelLabel = 'booking';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['member', 'membership.package', 'classSession.classType', 'classSession.instructor'])
                ->join('class_sessions', 'class_sessions.id', '=', 'bookings.class_session_id')
                ->select('bookings.*'))
            ->defaultSort('class_sessions.starts_at', 'desc')
            ->columns([
                TextColumn::make('classSession.starts_at')
                    ->label('Jadwal')
                    ->formatStateUsing(fn (Booking $record) => $record->classSession->starts_at->isoFormat('ddd D MMM YYYY · HH:mm'))
                    ->description(fn (Booking $record) => $record->classSession->classType->name.' · '.$record->classSession->instructor->name)
                    ->url(fn (Booking $record) => ClassSessionResource::getUrl('view', ['record' => $record->classSession])),
                BookingTable::flagsColumn(),
                BookingTable::packageColumn(),
                BookingTable::statusColumn(),
            ])
            ->recordActions(BookingTable::recordActions());
    }
}
