<?php

namespace App\Filament\Support;

use App\Actions\CancelBooking;
use App\Enums\BookingStatus;
use App\Exceptions\BookingNotPossible;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

/**
 * Kolom dan aksi peserta yang dipakai di beberapa tabel admin.
 */
class BookingTable
{
    public const FIRST_CLASS = 'Pertama kali';

    public const HAS_NOTES = 'Ada catatan';

    public static function memberColumn(): TextColumn
    {
        return TextColumn::make('member.nickname')
            ->label('Peserta')
            ->weight('semibold')
            ->description(fn (Booking $record) => $record->member->formattedWhatsapp())
            ->searchable(['nickname', 'whatsapp']);
    }

    /**
     * Tanda "Pertama kali" dan "Ada catatan" agar instruktur siap sebelum kelas.
     */
    public static function flagsColumn(): TextColumn
    {
        return TextColumn::make('flags')
            ->label('Tanda')
            ->state(fn (Booking $record) => array_values(array_filter([
                $record->is_first_class ? self::FIRST_CLASS : null,
                $record->member->hasInjuryNotes() ? self::HAS_NOTES : null,
            ])))
            ->badge()
            ->color(fn (string $state) => $state === self::FIRST_CLASS ? 'success' : 'warning')
            ->icon(fn (string $state) => $state === self::FIRST_CLASS ? Heroicon::OutlinedSparkles : Heroicon::OutlinedHeart)
            ->placeholder('—');
    }

    public static function notesColumn(): TextColumn
    {
        return TextColumn::make('member.injury_notes')
            ->label('Catatan')
            ->formatStateUsing(fn (?string $state) => Str::limit((string) $state, 48))
            ->tooltip(fn (Booking $record) => $record->member->injury_notes)
            ->color('warning')
            ->placeholder('—')
            ->wrap();
    }

    public static function packageColumn(): TextColumn
    {
        return TextColumn::make('membership.package.name')
            ->label('Paket')
            ->description(fn (Booking $record) => $record->membership
                ? "sisa {$record->membership->classes_remaining} kelas"
                : null)
            ->placeholder('—')
            ->toggleable();
    }

    public static function statusColumn(): TextColumn
    {
        return TextColumn::make('status')
            ->label('Status')
            ->badge();
    }

    /**
     * Kehadiran baru bisa dicatat di hari kelas atau sesudahnya.
     */
    private static function hasStarted(Booking $booking): bool
    {
        $startsAt = $booking->classSession->starts_at;

        return $startsAt->isPast() || $startsAt->isToday();
    }

    /**
     * @return array<Action|ActionGroup>
     */
    public static function recordActions(): array
    {
        return [
            Action::make('attended')
                ->label('Hadir')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->visible(fn (Booking $record) => self::hasStarted($record) && in_array($record->status, [BookingStatus::Confirmed, BookingStatus::NoShow], true))
                ->action(fn (Booking $record) => $record->update(['status' => BookingStatus::Attended])),
            ActionGroup::make([
                Action::make('noShow')
                    ->label('Tidak datang')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Booking $record) => self::hasStarted($record) && in_array($record->status, [BookingStatus::Confirmed, BookingStatus::Attended], true))
                    ->action(fn (Booking $record) => $record->update(['status' => BookingStatus::NoShow])),
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('gray')
                    ->visible(fn (Booking $record) => $record->status === BookingStatus::Confirmed)
                    ->modalHeading('Batalkan booking?')
                    ->modalDescription(fn (Booking $record) => $record->canCancelForFree()
                        ? 'Masih sebelum batas batal gratis, jadi kuota kembali ke paket.'
                        : 'Sudah lewat batas batal gratis. Kamu tetap bisa mengembalikan kuotanya.')
                    ->schema([
                        Toggle::make('refund')
                            ->label('Kembalikan 1 kelas ke paket')
                            ->default(fn (Booking $record) => $record->canCancelForFree()),
                    ])
                    ->action(function (Booking $record, array $data, CancelBooking $cancelBooking) {
                        try {
                            $cancelBooking->handle($record, (bool) $data['refund']);
                            Notification::make()->title('Booking dibatalkan')->success()->send();
                        } catch (BookingNotPossible $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                        }
                    }),
            ]),
        ];
    }
}
