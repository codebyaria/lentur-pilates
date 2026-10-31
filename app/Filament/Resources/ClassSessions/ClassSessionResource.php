<?php

namespace App\Filament\Resources\ClassSessions;

use App\Filament\Resources\ClassSessions\Pages\CreateClassSession;
use App\Filament\Resources\ClassSessions\Pages\EditClassSession;
use App\Filament\Resources\ClassSessions\Pages\ListClassSessions;
use App\Filament\Resources\ClassSessions\Pages\ViewClassSession;
use App\Filament\Resources\ClassSessions\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\ClassSessions\Schemas\ClassSessionForm;
use App\Filament\Resources\ClassSessions\Schemas\ClassSessionInfolist;
use App\Filament\Resources\ClassSessions\Tables\ClassSessionsTable;
use App\Models\ClassSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ClassSessionResource extends Resource
{
    protected static ?string $model = ClassSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Studio';

    protected static ?string $navigationLabel = 'Semua jadwal';

    protected static ?string $modelLabel = 'jadwal';

    protected static ?string $pluralModelLabel = 'jadwal';

    protected static ?int $navigationSort = 2;

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record instanceof ClassSession) {
            return static::getModelLabel();
        }

        return $record->classType->name.' · '.$record->starts_at->isoFormat('ddd D MMM, HH:mm');
    }

    /**
     * @return Builder<ClassSession>
     */
    public static function getEloquentQuery(): Builder
    {
        return ClassSession::query()
            ->with(['classType', 'instructor'])
            ->withSpotCounts();
    }

    public static function form(Schema $schema): Schema
    {
        return ClassSessionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClassSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassSessionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassSessions::route('/'),
            'create' => CreateClassSession::route('/create'),
            'view' => ViewClassSession::route('/{record}'),
            'edit' => EditClassSession::route('/{record}/edit'),
        ];
    }
}
