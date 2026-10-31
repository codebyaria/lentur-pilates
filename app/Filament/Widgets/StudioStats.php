<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\WeeklySchedule;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Member;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudioStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $week = ClassSession::query()
            ->with('classType')
            ->withSpotCounts()
            ->inWeekOf(now())
            ->whereNull('cancelled_at')
            ->get();

        $capacity = $week->sum(fn (ClassSession $session) => $session->effectiveCapacity());
        $booked = $week->sum(fn (ClassSession $session) => $session->bookedCount());

        $upcomingFull = ClassSession::query()
            ->with('classType')
            ->withSpotCounts()
            ->bookable()
            ->where('starts_at', '<=', now()->addDays(7))
            ->get()
            ->filter->isFull()
            ->count();

        $firstTimers = Booking::query()
            ->holdingSpot()
            ->where('is_first_class', true)
            ->whereIn('class_session_id', $week->modelKeys())
            ->count();

        return [
            Stat::make('Keterisian minggu ini', ($capacity > 0 ? round($booked / $capacity * 100) : 0).'%')
                ->description("{$booked} dari {$capacity} tempat · {$week->count()} kelas")
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->url(WeeklySchedule::getUrl())
                ->color('primary'),
            Stat::make('Pertama kali minggu ini', $firstTimers)
                ->description('Sambut yang hangat')
                ->descriptionIcon(Heroicon::OutlinedSparkles)
                ->color('success'),
            Stat::make('Kelas penuh, 7 hari ke depan', $upcomingFull)
                ->description($upcomingFull > 0 ? 'Arahkan ke jam berikutnya' : 'Semua masih lega')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color($upcomingFull > 0 ? 'warning' : 'success'),
            Stat::make('Member dengan paket aktif', Member::query()->has('currentMembership')->count())
                ->description(Member::query()->count().' member terdaftar')
                ->descriptionIcon(Heroicon::OutlinedTicket),
        ];
    }
}
