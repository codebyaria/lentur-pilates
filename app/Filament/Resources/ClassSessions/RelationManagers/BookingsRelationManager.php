<?php

namespace App\Filament\Resources\ClassSessions\RelationManagers;

use App\Actions\BookClass;
use App\Enums\BookingStatus;
use App\Exceptions\BookingNotPossible;
use App\Filament\Support\BookingTable;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property ClassSession $ownerRecord
 */
class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    protected static ?string $title = 'Peserta';

    protected static ?string $modelLabel = 'peserta';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['member', 'membership.package', 'classSession']))
            ->defaultSort('is_first_class', 'desc')
            ->columns([
                BookingTable::memberColumn(),
                BookingTable::flagsColumn(),
                BookingTable::notesColumn(),
                BookingTable::packageColumn(),
                BookingTable::statusColumn(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BookingStatus::class)
                    ->multiple()
                    ->default(array_map(fn (BookingStatus $status) => $status->value, BookingStatus::holdingSpot())),
            ])
            ->headerActions([
                $this->addParticipantAction(),
            ])
            ->recordActions(BookingTable::recordActions())
            ->emptyStateHeading('Belum ada peserta')
            ->emptyStateDescription('Begitu ada yang booking, namanya muncul di sini.');
    }

    private function addParticipantAction(): Action
    {
        return Action::make('addParticipant')
            ->label('Tambah peserta')
            ->icon(Heroicon::OutlinedPlus)
            ->disabled(fn () => ! $this->ownerRecord->isBookable())
            ->tooltip(fn () => $this->ownerRecord->isFull() ? 'Kelas sudah penuh' : null)
            ->modalDescription('Satu kelas dipotong dari paket aktif member.')
            ->schema([
                Select::make('member_id')
                    ->label('Member')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => Member::query()
                        ->where(fn (Builder $query) => $query
                            ->where('nickname', 'like', "%{$search}%")
                            ->orWhere('whatsapp', 'like', '%'.Member::normalizeWhatsapp($search).'%'))
                        ->whereDoesntHave('bookings', fn (Builder $query) => $query
                            ->where('class_session_id', $this->ownerRecord->id)
                            ->holdingSpot())
                        ->limit(20)
                        ->get()
                        ->mapWithKeys(fn (Member $member) => [$member->id => "{$member->nickname} · {$member->formattedWhatsapp()}"]))
                    ->getOptionLabelUsing(fn ($value) => Member::query()->whereKey($value)->value('nickname'))
                    ->required(),
            ])
            ->action(function (array $data, BookClass $bookClass) {
                try {
                    $booking = $bookClass->handle($this->ownerRecord, Member::query()->whereKey($data['member_id'])->firstOrFail());

                    Notification::make()
                        ->title("{$booking->member->nickname} terdaftar")
                        ->body($booking->is_first_class ? 'Ini kelas pertamanya. Sambut yang hangat!' : null)
                        ->success()
                        ->send();
                } catch (BookingNotPossible $exception) {
                    Notification::make()->title($exception->getMessage())->danger()->send();
                }
            });
    }

    /**
     * @param  Booking  $record
     */
    protected function canDelete($record): bool
    {
        return false;
    }
}
