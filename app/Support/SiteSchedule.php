<?php

namespace App\Support;

use App\Enums\ClassLevel;
use App\Http\Resources\Site\ScheduleSessionResource;
use App\Models\ClassSession;
use Illuminate\Http\Request;

/**
 * Jadwal untuk situs publik (landing dan langkah 1 booking): hari ini + 6 hari ke depan.
 */
class SiteSchedule
{
    public const DAYS = 7;

    /**
     * @return array{days: list<array<string, mixed>>, schedule: array<int, mixed>, levels: list<array{value: string, label: string}>}
     */
    public static function props(Request $request): array
    {
        $today = today();

        $sessions = ClassSession::query()
            ->with(['classType', 'instructor'])
            ->withSpotCounts()
            ->bookable()
            ->where('starts_at', '<', $today->addDays(self::DAYS))
            ->orderBy('starts_at')
            ->get();

        return [
            'days' => array_map(fn (int $offset) => [
                'key' => $today->addDays($offset)->toDateString(),
                'label' => $today->addDays($offset)->isoFormat('dddd'),
                'shortLabel' => $today->addDays($offset)->isoFormat('ddd'),
                'date' => $today->addDays($offset)->isoFormat('D MMM'),
                'isToday' => $offset === 0,
            ], range(0, self::DAYS - 1)),
            'schedule' => ScheduleSessionResource::collection($sessions)->resolve($request),
            'levels' => array_map(fn (ClassLevel $level) => [
                'value' => $level->value,
                'label' => $level->getLabel(),
            ], ClassLevel::cases()),
        ];
    }
}
