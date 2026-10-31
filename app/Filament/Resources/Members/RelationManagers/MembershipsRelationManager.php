<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Actions\PurchasePackage;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Package;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * @property Member $ownerRecord
 */
class MembershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'memberships';

    protected static ?string $title = 'Paket';

    protected static ?string $modelLabel = 'paket';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('classes_remaining')
                ->label('Sisa kelas')
                ->numeric()
                ->minValue(0)
                ->required(),
            DateTimePicker::make('expires_at')
                ->label('Berlaku sampai')
                ->seconds(false)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('purchased_at', 'desc')
            ->columns([
                TextColumn::make('package.name')
                    ->label('Paket')
                    ->weight('semibold')
                    ->description(fn (Membership $record) => $record->payment_reference),
                TextColumn::make('classes_remaining')
                    ->label('Sisa kelas')
                    ->formatStateUsing(fn (Membership $record) => "{$record->classes_remaining} / {$record->classes_total}")
                    ->badge()
                    ->color(fn (Membership $record) => $record->isUsable() ? 'success' : 'gray'),
                TextColumn::make('price_paid')
                    ->label('Dibayar')
                    ->formatStateUsing(fn (int $state) => Package::formatRupiah($state)),
                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge(),
                TextColumn::make('purchased_at')
                    ->label('Dibeli')
                    ->date('d M Y'),
                TextColumn::make('expires_at')
                    ->label('Berlaku sampai')
                    ->date('d M Y')
                    ->color(fn (Membership $record) => $record->expires_at->isPast() ? 'danger' : null),
            ])
            ->headerActions([
                Action::make('purchase')
                    ->label('Tambah paket')
                    ->icon(Heroicon::OutlinedPlus)
                    ->modalDescription('Demo: pembayaran disimulasikan dan langsung lunas.')
                    ->schema([
                        Select::make('package_id')
                            ->label('Paket')
                            ->options(fn () => Package::query()->available()->get()->mapWithKeys(
                                fn (Package $package) => [$package->id => "{$package->name} · ".Package::formatRupiah($package->price)],
                            ))
                            ->required(),
                    ])
                    ->action(function (array $data, PurchasePackage $purchasePackage) {
                        $membership = $purchasePackage->handle($this->ownerRecord, Package::query()->whereKey($data['package_id'])->firstOrFail());

                        Notification::make()
                            ->title("Paket {$membership->package->name} ditambahkan")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah kuota'),
            ]);
    }
}
