<?php

namespace App\Filament\Resources\ClassSessions\Schemas;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Filament\Resources\ClassSessions\Tables\ClassSessionsTable;
use App\Models\ClassSession;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassSessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(['default' => 2, 'lg' => 4])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('classType.name')
                            ->label('Kelas')
                            ->weight('bold')
                            ->helperText(fn (ClassSession $record) => $record->classType->kind->getLabel()),
                        TextEntry::make('starts_at')
                            ->label('Waktu')
                            ->formatStateUsing(fn (ClassSession $record) => $record->starts_at->isoFormat('dddd, D MMMM · HH:mm').'–'.$record->ends_at->format('H:i'))
                            ->helperText(fn (ClassSession $record) => $record->room),
                        TextEntry::make('instructor.name')
                            ->label('Instruktur')
                            ->helperText(fn (ClassSession $record) => $record->classType->level->getLabel()),
                        TextEntry::make('active_bookings_count')
                            ->label('Keterisian')
                            ->state(fn (ClassSession $record) => $record->isCancelled()
                                ? 'Dibatalkan'
                                : "{$record->bookedCount()}/{$record->effectiveCapacity()}")
                            ->badge()
                            ->color(fn (ClassSession $record) => ClassSessionsTable::occupancyColor($record))
                            ->helperText(fn (ClassSession $record) => $record->isFull() ? 'Penuh' : "sisa {$record->spotsLeft()} tempat"),
                        TextEntry::make('alternative')
                            ->label('Saran jam lain untuk yang belum kebagian')
                            ->columnSpanFull()
                            ->visible(fn (ClassSession $record) => $record->isFull() && $record->starts_at->isFuture())
                            ->state(function (ClassSession $record) {
                                $alternative = $record->nextAvailableAlternative();

                                return $alternative
                                    ? ClassSessionResource::getRecordTitle($alternative)." (sisa {$alternative->spotsLeft()})"
                                    : 'Belum ada jadwal lain yang lega minggu ini.';
                            })
                            ->url(fn (ClassSession $record) => ($alternative = $record->nextAvailableAlternative())
                                ? ClassSessionResource::getUrl('view', ['record' => $alternative])
                                : null)
                            ->color('primary'),
                    ]),
            ]);
    }
}
