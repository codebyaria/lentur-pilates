<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Booking ditolak karena aturan studio. Pesannya aman ditampilkan ke pengguna.
 * `field` menunjuk bagian formulir booking yang perlu diperbaiki.
 */
class BookingNotPossible extends RuntimeException
{
    final public function __construct(string $message, public readonly string $field = 'class_session_id')
    {
        parent::__construct($message);
    }

    public static function sessionNotBookable(): static
    {
        return new static(__('Kelas ini sudah lewat atau dibatalkan.'));
    }

    public static function sessionFull(): static
    {
        return new static(__('Kelas ini sudah ramai. Jam berikutnya masih lega.'));
    }

    public static function alreadyBooked(): static
    {
        return new static(__('Sudah terdaftar di kelas ini.'));
    }

    public static function noUsablePackage(): static
    {
        return new static(__('Belum ada paket aktif dengan sisa kelas.'), 'package');
    }

    public static function introAlreadyUsed(): static
    {
        return new static(__('Kelas Pertama hanya untuk kunjungan pertama. Pilih paket lain, ya.'), 'package');
    }

    public static function packageUnavailable(): static
    {
        return new static(__('Paket ini sedang tidak dijual.'), 'package');
    }

    public static function notCancellable(): static
    {
        return new static(__('Booking ini tidak bisa dibatalkan lagi.'));
    }
}
