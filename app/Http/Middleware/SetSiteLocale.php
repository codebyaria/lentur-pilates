<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Situs publik: bahasa Indonesia di /, bahasa Inggris di /en.
 * Rute /en memberi default `locale = en`.
 */
class SetSiteLocale
{
    public const LOCALES = ['id', 'en'];

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route()?->defaults['locale'] ?? 'id';
        $locale = in_array($locale, self::LOCALES, true) ? $locale : 'id';

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        return $next($request);
    }
}
