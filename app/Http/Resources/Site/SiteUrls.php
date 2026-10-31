<?php

namespace App\Http\Resources\Site;

/**
 * URL situs publik yang mengikuti bahasa aktif.
 */
class SiteUrls
{
    public static function home(?string $locale = null): string
    {
        return ($locale ?? app()->getLocale()) === 'en' ? route('home.en') : route('home');
    }

    public static function booking(?int $sessionId = null, ?string $locale = null): string
    {
        $route = ($locale ?? app()->getLocale()) === 'en' ? 'booking.en' : 'booking';

        return route($route, array_filter(['jadwal' => $sessionId]));
    }
}
