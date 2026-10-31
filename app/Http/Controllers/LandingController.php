<?php

namespace App\Http\Controllers;

use App\Http\Resources\Site\ClassTypeResource;
use App\Http\Resources\Site\InstructorResource;
use App\Http\Resources\Site\PackageResource;
use App\Http\Resources\Site\SiteUrls;
use App\Models\ClassType;
use App\Models\Instructor;
use App\Models\Package;
use App\Support\SiteSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $packages = Package::query()->available()->get();

        return Inertia::render('landing', [
            ...SiteSchedule::props($request),
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
