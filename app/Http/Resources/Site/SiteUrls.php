<?php

namespace App\Http\Resources\Site;

use App\Models\Booking;
use Illuminate\Support\Facades\URL;

/**
 * URL situs publik yang mengikuti bahasa aktif.
 */
class SiteUrls
{
    public static function home(?string $locale = null): string
    {
        return route(self::name('home', $locale));
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public static function booking(?int $sessionId = null, ?string $locale = null, array $query = []): string
    {
        return route(self::name('booking', $locale), array_filter(['jadwal' => $sessionId, ...$query]));
    }

    public static function bookingStore(?string $locale = null): string
    {
        return route(self::name('booking.store', $locale));
    }

    /**
     * Ditandatangani: hanya yang baru booking yang bisa membuka konfirmasinya.
     */
    public static function confirmation(Booking $booking, ?string $locale = null): string
    {
        return URL::signedRoute(self::name('booking.confirmed', $locale), ['booking' => $booking->code]);
    }

    public static function calendar(Booking $booking, ?string $locale = null): string
    {
        return URL::signedRoute(self::name('booking.calendar', $locale), ['booking' => $booking->code]);
    }

    private static function name(string $route, ?string $locale): string
    {
        return ($locale ?? app()->getLocale()) === 'en' ? "{$route}.en" : $route;
    }
}
