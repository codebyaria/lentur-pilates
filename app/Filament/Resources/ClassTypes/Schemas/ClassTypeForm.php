<?php

namespace App\Filament\Resources\ClassTypes\Schemas;

use App\Enums\ClassKind;
use App\Enums\ClassLevel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ClassTypeForm
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
                            ->label('Nama kelas')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create'
                                ? $set('slug', Str::slug((string) $state))
                                : null),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('kind')
                            ->label('Jenis')
                            ->options(ClassKind::class)
                            ->required(),
                        Select::make('level')
                            ->label('Level')
                            ->options(ClassLevel::class)
                            ->required(),
                        TextInput::make('summary')
                            ->label('Ringkasan')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('duration_minutes')
                            ->label('Durasi')
                            ->suffix('menit')
                            ->numeric()
                            ->default(50)
                            ->required(),
                        TextInput::make('capacity')
                            ->label('Kapasitas')
                            ->suffix('orang')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->required(),
                        Select::make('color')
                            ->label('Warna kartu')
                            ->options(['lime' => 'Limau', 'pink' => 'Merah muda', 'blue' => 'Biru'])
                            ->default('lime')
                            ->required(),
                        Toggle::make('is_intro')
                            ->label('Kelas pertama')
                            ->helperText('Selalu tampil paling atas di booking.'),
                    ]),
            ]);
    }
}
