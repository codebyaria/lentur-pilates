<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ClassSessions\ClassSessionResource;
use App\Filament\Resources\ClassSessions\Tables\ClassSessionsTable;
use App\Models\ClassSession;
use BackedEnum;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * Satu minggu dalam sekali lihat: tiap kelas dengan keterisian,
 * jumlah yang pertama kali, dan yang punya catatan.
 */
class WeeklySchedule extends Page
{
    protected string $view = 'filament.pages.weekly-schedule';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Studio';

    protected static ?string $navigationLabel = 'Jadwal mingguan';

    protected static ?string $title = 'Jadwal mingguan';

    protected static ?string $slug = 'jadwal-mingguan';

    protected static ?int $navigationSort = 1;

    /** Senin dari minggu yang ditampilkan (Y-m-d). */
    #[Url(as: 'minggu')]
    public string $week = '';

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
    }

    public function monday(): CarbonImmutable
    {
        $date = rescue(fn () => CarbonImmutable::parse($this->week), now(), report: false);

        return CarbonImmutable::parse($date)->startOfWeek(CarbonInterface::MONDAY);
    }

    public function getSubheading(): string
    {
        return ClassSessionsTable::weekLabel($this->monday());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previousWeek')
                ->label('Minggu lalu')
                ->icon(Heroicon::OutlinedChevronLeft)
                ->color('gray')
                ->action(fn () => $this->week = $this->monday()->subWeek()->toDateString()),
            Action::make('thisWeek')
                ->label('Minggu ini')
                ->color('gray')
                ->disabled(fn () => $this->monday()->isSameDay(now()->startOfWeek(CarbonInterface::MONDAY)))
                ->action(fn () => $this->week = now()->startOfWeek(CarbonInterface::MONDAY)->toDateString()),
            Action::make('nextWeek')
                ->label('Minggu depan')
                ->icon(Heroicon::OutlinedChevronRight)
                ->iconPosition('after')
                ->color('gray')
                ->action(fn () => $this->week = $this->monday()->addWeek()->toDateString()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $monday = $this->monday();

        $sessions = ClassSession::query()
            ->with(['classType', 'instructor'])
            ->withSpotCounts()
            ->withCount([
                'activeBookings as first_timers_count' => fn (Builder $query) => $query->where('is_first_class', true),
                'activeBookings as with_notes_count' => fn (Builder $query) => $query->whereHas(
                    'member',
                    fn (Builder $query) => $query->whereNotNull('injury_notes'),
                ),
            ])
            ->inWeekOf($monday)
            ->get();

        $active = $sessions->reject->isCancelled();
        $capacity = $active->sum(fn (ClassSession $session) => $session->effectiveCapacity());
        $booked = $active->sum(fn (ClassSession $session) => $session->bookedCount());

        $days = collect(range(0, 6))->map(function (int $offset) use ($monday, $sessions) {
            $date = $monday->addDays($offset);

            return [
                'label' => $date->isoFormat('dddd'),
                'date' => $date->isoFormat('D MMM'),
                'isToday' => $date->isToday(),
                'sessions' => $sessions
                    ->filter(fn (ClassSession $session) => $session->starts_at->isSameDay($date))
                    ->map(fn (ClassSession $session) => $this->presentSession($session))
                    ->values(),
            ];
        });

        return [
            'days' => $days,
            'summary' => [
                'classes' => $active->count(),
                'fill' => $capacity > 0 ? (int) round($booked / $capacity * 100) : 0,
                'booked' => $booked,
                'capacity' => $capacity,
                'full' => $active->filter->isFull()->count(),
                'firstTimers' => (int) $active->sum('first_timers_count'),
                'withNotes' => (int) $active->sum('with_notes_count'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentSession(ClassSession $session): array
    {
        $capacity = $session->effectiveCapacity();
        $booked = $session->bookedCount();

        return [
            'url' => ClassSessionResource::getUrl('view', ['record' => $session]),
            'time' => $session->starts_at->format('H:i'),
            'name' => $session->classType->name,
            'instructor' => $session->instructor->name,
            'level' => $session->classType->level->getLabel(),
            'color' => $session->classType->color,
            'booked' => $booked,
            'capacity' => $capacity,
            'percent' => $capacity > 0 ? min(100, (int) round($booked / $capacity * 100)) : 0,
            'state' => match (true) {
                $session->isCancelled() => 'cancelled',
                $session->isFull() => 'full',
                $session->starts_at->isPast() => 'past',
                default => 'open',
            },
            'firstTimers' => (int) $session->getAttribute('first_timers_count'),
            'withNotes' => (int) $session->getAttribute('with_notes_count'),
        ];
    }
}
