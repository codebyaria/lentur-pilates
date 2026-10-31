<?php

namespace App\Filament\Resources\ClassSessions\Tables;

use App\Actions\CancelClassSession;
use App\Enums\ClassLevel;
use App\Models\ClassSession;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ClassSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at')
            ->defaultGroup(
                Group::make('starts_at')
                    ->label('Hari')
                    ->date()
                    ->collapsible()
                    // Htmlable: Filament tidak mem-parse ulang judul sebagai tanggal.
                    ->getTitleFromRecordUsing(fn (ClassSession $record) => new HtmlString(e($record->starts_at->isoFormat('dddd, D MMMM')))),
            )
            ->columns([
                TextColumn::make('starts_at')
                    ->label('Jam')
                    ->formatStateUsing(fn (ClassSession $record) => $record->starts_at->format('H:i').'–'.$record->ends_at->format('H:i'))
                    ->sortable(),
                TextColumn::make('classType.name')
                    ->label('Kelas')
                    ->weight('semibold')
                    ->description(fn (ClassSession $record) => $record->classType->kind->getLabel().' · '.$record->room)
                    ->searchable(),
                TextColumn::make('classType.level')
                    ->label('Level')
                    ->badge(),
                TextColumn::make('instructor.name')
                    ->label('Instruktur')
                    ->searchable(),
                self::occupancyColumn(),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Periode')
                    ->options([
                        'upcoming' => 'Mulai hari ini',
                        'this_week' => 'Minggu ini',
                        'next_week' => 'Minggu depan',
                        'past' => 'Sudah lewat',
                    ])
                    ->default('upcoming')
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'upcoming' => $query->where('starts_at', '>=', today()),
                        'this_week' => $query->whereBetween('starts_at', ClassSession::weekRange(now())),
                        'next_week' => $query->whereBetween('starts_at', ClassSession::weekRange(now()->addWeek())),
                        'past' => $query->where('starts_at', '<', now()),
                        default => $query,
                    }),
                SelectFilter::make('level')
                    ->label('Level')
                    ->options(ClassLevel::class)
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $level) => $query->whereHas('classType', fn (Builder $query) => $query->where('level', $level)),
                    )),
                SelectFilter::make('instructor')
                    ->label('Instruktur')
                    ->relationship('instructor', 'name'),
            ])
            ->recordActions([
                ViewAction::make()->label('Peserta'),
                ActionGroup::make([
                    EditAction::make(),
                    self::cancelAction(),
                ]),
            ]);
    }

    public static function occupancyColumn(): TextColumn
    {
        return TextColumn::make('active_bookings_count')
            ->label('Keterisian')
            ->formatStateUsing(fn (ClassSession $record) => $record->isCancelled()
                ? 'Dibatalkan'
                : "{$record->bookedCount()}/{$record->effectiveCapacity()}")
            ->description(fn (ClassSession $record) => match (true) {
                $record->isCancelled() => null,
                $record->isFull() => 'Penuh',
                default => "sisa {$record->spotsLeft()}",
            })
            ->badge()
            ->color(fn (ClassSession $record) => self::occupancyColor($record))
            ->sortable();
    }

    public static function occupancyColor(ClassSession $session): string
    {
        if ($session->isCancelled()) {
            return 'gray';
        }

        $ratio = $session->bookedCount() / max(1, $session->effectiveCapacity());

        return match (true) {
            $ratio >= 1 => 'danger',
            $ratio >= 0.75 => 'warning',
            default => 'success',
        };
    }

    public static function cancelAction(): Action
    {
        return Action::make('cancelSession')
            ->label('Batalkan kelas')
            ->icon(Heroicon::OutlinedNoSymbol)
            ->color('danger')
            ->visible(fn (ClassSession $record) => ! $record->isCancelled() && $record->starts_at->isFuture())
            ->requiresConfirmation()
            ->modalHeading('Batalkan kelas ini?')
            ->modalDescription('Semua peserta terdaftar ikut dibatalkan dan kuota kelasnya dikembalikan ke paket masing-masing.')
            ->action(function (ClassSession $record, CancelClassSession $cancelClassSession) {
                $count = $cancelClassSession->handle($record);

                Notification::make()
                    ->title('Kelas dibatalkan')
                    ->body("{$count} peserta mendapat kuotanya kembali.")
                    ->success()
                    ->send();
            });
    }

    public static function weekLabel(CarbonInterface $monday): string
    {
        $sunday = $monday->copy()->addDays(6);

        return $monday->isoFormat('D MMM').' – '.$sunday->isoFormat('D MMM YYYY');
    }
}
