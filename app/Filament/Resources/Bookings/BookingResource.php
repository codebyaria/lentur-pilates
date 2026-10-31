<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Tables\BookingsTable;
use App\Models\Booking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Studio';

    protected static ?string $navigationLabel = 'Peserta';

    protected static ?string $modelLabel = 'peserta';

    protected static ?string $pluralModelLabel = 'peserta';

    protected static ?int $navigationSort = 3;

    /**
     * @return Builder<Booking>
     */
    public static function getEloquentQuery(): Builder
    {
        return Booking::query()
            ->with(['member', 'membership.package', 'classSession.classType', 'classSession.instructor']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return BookingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
        ];
    }
}
