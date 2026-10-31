<?php

namespace App\Support;

use App\Models\Booking;
use Carbon\CarbonInterface;

/**
 * "Simpan ke kalender": file .ics (Apple/Outlook/apa pun) dan tautan Google Calendar.
 * Tidak ada integrasi; hanya data acara.
 */
class BookingCalendar
{
    public static function title(Booking $booking): string
    {
        return $booking->classSession->classType->localized('name').' · Lentur Pilates';
    }

    public static function details(Booking $booking): string
    {
        return implode("\n", [
            __('Bersama :name.', ['name' => $booking->classSession->instructor->name]),
            __('Kode booking: :code', ['code' => $booking->code]),
            __('Bawa air minum, tinggalkan rasa sungkan.'),
        ]);
    }

    public static function location(): string
    {
        return config('lentur.studio.name').', '.config('lentur.studio.address');
    }

    public static function ics(Booking $booking): string
    {
        $session = $booking->classSession;

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Lentur Pilates//Demo//ID',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:'.$booking->code.'@lentur.test',
            'DTSTAMP:'.self::utc(now()),
            'DTSTART:'.self::utc($session->starts_at),
            'DTEND:'.self::utc($session->ends_at),
            'SUMMARY:'.self::escape(self::title($booking)),
            'LOCATION:'.self::escape(self::location()),
            'DESCRIPTION:'.self::escape(self::details($booking)),
            'BEGIN:VALARM',
            'TRIGGER:-PT2H',
            'ACTION:DISPLAY',
            'DESCRIPTION:'.self::escape(self::title($booking)),
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines)."\r\n";
    }

    public static function googleUrl(Booking $booking): string
    {
        $session = $booking->classSession;

        return 'https://calendar.google.com/calendar/render?'.http_build_query([
            'action' => 'TEMPLATE',
            'text' => self::title($booking),
            'dates' => self::utc($session->starts_at).'/'.self::utc($session->ends_at),
            'details' => self::details($booking),
            'location' => self::location(),
        ]);
    }

    private static function utc(CarbonInterface $date): string
    {
        return $date->copy()->utc()->format('Ymd\THis\Z');
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', ';', ',', "\n"], ['\\\\', '\;', '\,', '\n'], $text);
    }
}
