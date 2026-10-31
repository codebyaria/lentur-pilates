<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\Member;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(['default' => 2, 'lg' => 4])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('nickname')
                            ->label('Nama panggilan')
                            ->weight('bold'),
                        TextEntry::make('whatsapp')
                            ->label('WhatsApp')
                            ->copyable(),
                        IconEntry::make('is_new_to_pilates')
                            ->label('Pertama kali pilates')
                            ->boolean(),
                        TextEntry::make('referredBy.nickname')
                            ->label('Diajak oleh')
                            ->placeholder('—'),
                        TextEntry::make('injury_notes')
                            ->label('Catatan cedera')
                            ->color('warning')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                        TextEntry::make('currentMembership.package.name')
                            ->label('Paket aktif')
                            ->placeholder('Tidak ada')
                            ->helperText(fn (Member $record) => $record->currentMembership
                                ? "sisa {$record->currentMembership->classes_remaining} dari {$record->currentMembership->classes_total} kelas"
                                : null),
                        TextEntry::make('classes_taken_count')
                            ->label('Kelas diikuti'),
                    ]),
            ]);
    }
}
