<?php

namespace App\Http\Controllers;

use App\Enums\ClassLevel;
use App\Http\Resources\Site\ClassTypeResource;
use App\Http\Resources\Site\InstructorResource;
use App\Http\Resources\Site\PackageResource;
use App\Http\Resources\Site\ScheduleSessionResource;
use App\Http\Resources\Site\SiteUrls;
use App\Models\ClassSession;
use App\Models\ClassType;
use App\Models\Instructor;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /** Jadwal yang ditampilkan: hari ini + 6 hari ke depan. */
    private const SCHEDULE_DAYS = 7;

    public function __invoke(Request $request): Response
    {
        $today = today();

        $sessions = ClassSession::query()
            ->with(['classType', 'instructor'])
            ->withSpotCounts()
            ->bookable()
            ->where('starts_at', '<', $today->addDays(self::SCHEDULE_DAYS))
            ->orderBy('starts_at')
            ->get();

        $packages = Package::query()->available()->get();

        return Inertia::render('landing', [
            'days' => collect(range(0, self::SCHEDULE_DAYS - 1))->map(fn (int $offset) => [
                'key' => $today->addDays($offset)->toDateString(),
                'label' => $today->addDays($offset)->isoFormat('dddd'),
                'shortLabel' => $today->addDays($offset)->isoFormat('ddd'),
                'date' => $today->addDays($offset)->isoFormat('D MMM'),
                'isToday' => $offset === 0,
            ]),
            'schedule' => ScheduleSessionResource::collection($sessions)->resolve($request),
            'levels' => collect(ClassLevel::cases())->map(fn (ClassLevel $level) => [
                'value' => $level->value,
                'label' => $level->getLabel(),
            ]),
            'classTypes' => ClassTypeResource::collection(ClassType::query()->ordered()->get())->resolve($request),
            'packages' => PackageResource::collection($packages)->resolve($request),
            'cancellationHours' => (int) config('lentur.free_cancellation_hours'),
            'instructors' => InstructorResource::collection(Instructor::query()->active()->get())->resolve($request),
            'studio' => [
                'name' => config('lentur.studio.name'),
                'address' => config('lentur.studio.address'),
            ],
            'urls' => [
                'home' => SiteUrls::home(),
                'booking' => SiteUrls::booking(),
                'alternateHome' => SiteUrls::home(app()->getLocale() === 'en' ? 'id' : 'en'),
            ],
        ]);
    }
}
