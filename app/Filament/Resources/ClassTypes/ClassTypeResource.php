<?php

namespace App\Filament\Resources\ClassTypes;

use App\Filament\Resources\ClassTypes\Pages\CreateClassType;
use App\Filament\Resources\ClassTypes\Pages\EditClassType;
use App\Filament\Resources\ClassTypes\Pages\ListClassTypes;
use App\Filament\Resources\ClassTypes\Schemas\ClassTypeForm;
use App\Filament\Resources\ClassTypes\Tables\ClassTypesTable;
use App\Models\ClassType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClassTypeResource extends Resource
{
    protected static ?string $model = ClassType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan studio';

    protected static ?string $navigationLabel = 'Jenis kelas';

    protected static ?string $modelLabel = 'jenis kelas';

    protected static ?string $pluralModelLabel = 'jenis kelas';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ClassTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassTypes::route('/'),
            'create' => CreateClassType::route('/create'),
            'edit' => EditClassType::route('/{record}/edit'),
        ];
    }
}
