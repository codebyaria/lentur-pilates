<?php

namespace App\Filament\Resources\Instructors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InstructorForm
{
    /**
     * Sama dengan urutan FIGURES di resources/js/components/lentur/body-figures.tsx.
     *
     * @var array<int, string>
     */
    public const FIGURES = [
        0 => 'Tinggi ramping, meraih ke atas',
        1 => 'Berbadan lebar, merentangkan tangan',
        2 => 'Mungil, melambai',
        3 => 'Pengguna kursi roda',
        4 => 'Tinggi besar, santai',
        5 => 'Sedang, satu tangan ke atas',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create'
                                ? $set('slug', Str::slug((string) $state))
                                : null),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('tagline')
                            ->label('Tagline')
                            ->helperText('Satu kalimat, seperti cara dia menyapa.')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('bio')
                            ->label('Cerita singkat')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        TagsInput::make('specialties')
                            ->label('Keahlian')
                            ->suggestions(['Mat', 'Reformer', 'Kelas pertama', 'Mobilitas', 'Postur'])
                            ->required(),
                        Select::make('figure')
                            ->label('Ilustrasi avatar')
                            ->options(self::FIGURES)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif mengajar')
                            ->default(true),
                    ]),
                Section::make('English (/en)')
                    ->description('Opsional. Kosong = situs /en memakai teks Indonesia.')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('tagline_en')
                            ->label('Tagline')
                            ->columnSpanFull(),
                        Textarea::make('bio_en')
                            ->label('Short story')
                            ->rows(3)
                            ->columnSpanFull(),
                        TagsInput::make('specialties_en')
                            ->label('Specialties'),
                    ]),
            ]);
    }
}
