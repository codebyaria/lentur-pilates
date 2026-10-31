<?php

namespace App\Http\Controllers;

use App\Http\Resources\Site\ScheduleSessionResource;
use App\Http\Resources\Site\SiteUrls;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Sementara, sampai alur booking 4 langkah dibuat. Tombol "Pesan" di landing sudah menunjuk ke sini.
 */
class BookingPlaceholderController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $session = ClassSession::query()
            ->with(['classType', 'instructor'])
            ->withSpotCounts()
            ->find($request->integer('jadwal') ?: null);

        return Inertia::render('booking/coming-soon', [
            'session' => $session ? (new ScheduleSessionResource($session))->resolve($request) : null,
            'sessionDate' => $session?->starts_at->isoFormat('dddd, D MMMM'),
            'urls' => [
                'home' => SiteUrls::home(),
            ],
        ]);
    }
}
