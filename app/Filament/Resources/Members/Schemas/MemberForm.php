<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kenalan')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nickname')
                            ->label('Nama panggilan')
                            ->required()
                            ->maxLength(40),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->label('Email (opsional)')
                            ->email(),
                        Select::make('referred_by_id')
                            ->label('Diajak oleh')
                            ->relationship('referredBy', 'nickname')
                            ->searchable()
                            ->preload(),
                        Toggle::make('is_new_to_pilates')
                            ->label('Pertama kali pilates?')
                            ->columnSpanFull(),
                        Textarea::make('injury_notes')
                            ->label('Catatan cedera (opsional)')
                            ->helperText('Hanya terlihat oleh instruktur dan admin.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
