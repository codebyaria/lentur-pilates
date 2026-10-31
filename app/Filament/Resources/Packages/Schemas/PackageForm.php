<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama paket')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create'
                                ? $set('slug', Str::slug((string) $state))
                                : null),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('summary')
                            ->label('Ringkasan')
                            ->helperText('Satu kalimat pendek, nada teman.')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('class_count')
                            ->label('Jumlah kelas')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        TextInput::make('price')
                            ->label('Harga')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('validity_days')
                            ->label('Masa berlaku')
                            ->suffix('hari')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                        TextInput::make('sort')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_intro')
                            ->label('Paket kelas pertama')
                            ->helperText('Selalu tampil paling atas.'),
                        Toggle::make('is_shareable')
                            ->label('Bisa dipakai berdua'),
                        Toggle::make('is_active')
                            ->label('Dijual')
                            ->default(true),
                    ]),
                Section::make('English (/en)')
                    ->description('Opsional. Kosong = situs /en memakai teks Indonesia.')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name_en')
                            ->label('Name'),
                        TextInput::make('summary_en')
                            ->label('Summary'),
                    ]),
            ]);
    }
}
