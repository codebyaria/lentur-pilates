<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Booking ditolak karena aturan studio. Pesannya aman ditampilkan ke pengguna.
 */
class BookingNotPossible extends RuntimeException
{
    public static function sessionNotBookable(): self
    {
        return new self(__('Kelas ini sudah lewat atau dibatalkan.'));
    }

    public static function sessionFull(): self
    {
        return new self(__('Kelas ini sudah ramai. Jam berikutnya masih lega.'));
    }

    public static function alreadyBooked(): self
    {
        return new self(__('Sudah terdaftar di kelas ini.'));
    }

    public static function noUsablePackage(): self
    {
        return new self(__('Belum ada paket aktif dengan sisa kelas.'));
    }

    public static function notCancellable(): self
    {
        return new self(__('Booking ini tidak bisa dibatalkan lagi.'));
    }
}
