<?php

namespace App\Filament\Resources\ClassSessions\Schemas;

use App\Models\ClassType;
use App\Models\Instructor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ClassSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('class_type_id')
                            ->label('Kelas')
                            ->options(fn () => ClassType::query()->ordered()->pluck('name', 'id'))
                            ->live()
                            ->required(),
                        Select::make('instructor_id')
                            ->label('Instruktur')
                            ->options(fn () => Instructor::query()->active()->pluck('name', 'id'))
                            ->required(),
                        DateTimePicker::make('starts_at')
                            ->label('Mulai')
                            ->seconds(false)
                            ->minutesStep(15)
                            ->helperText('Jam selesai dihitung dari durasi kelas.')
                            ->required(),
                        TextInput::make('room')
                            ->label('Ruang')
                            ->datalist(['Studio Utama', 'Ruang Reformer'])
                            ->default('Studio Utama')
                            ->required(),
                        TextInput::make('capacity')
                            ->label('Kapasitas khusus')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->placeholder(fn (Get $get) => (string) (ClassType::query()->whereKey($get('class_type_id'))->value('capacity') ?? ''))
                            ->helperText('Kosongkan untuk ikut kapasitas kelas.'),
                    ]),
            ]);
    }
}
