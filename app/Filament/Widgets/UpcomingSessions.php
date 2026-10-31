<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Filament\Resources\ClassSessions\Tables\ClassSessionsTable;
use App\Models\ClassSession;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingSessions extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Hari ini & besok';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => ClassSession::query()
                ->with(['classType', 'instructor'])
                ->withSpotCounts()
                ->withCount(['activeBookings as first_timers_count' => fn ($query) => $query->where('is_first_class', true)])
                ->whereNull('cancelled_at')
                ->whereBetween('starts_at', [now()->subHour(), now()->addDay()->endOfDay()])
                ->orderBy('starts_at'))
            ->paginated(false)
            ->recordUrl(fn (ClassSession $record) => ClassSessionResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('starts_at')
                    ->label('Waktu')
                    ->formatStateUsing(fn (ClassSession $record) => $record->starts_at->isoFormat('ddd · HH:mm')),
                TextColumn::make('classType.name')
                    ->label('Kelas')
                    ->weight('semibold')
                    ->description(fn (ClassSession $record) => $record->instructor->name),
                ClassSessionsTable::occupancyColumn()->sortable(false),
                TextColumn::make('first_timers_count')
                    ->label('Pertama kali')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (int $state) => $state > 0 ? "{$state} orang" : null)
                    ->placeholder('—'),
            ])
            ->emptyStateHeading('Tidak ada kelas hari ini dan besok')
            ->emptyStateDescription('Waktunya merapikan matras.');
    }
}
