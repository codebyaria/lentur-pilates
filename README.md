# Lentur Pilates

> Gerak dulu. Sempurna belakangan.

Demo konsep untuk studio pilates (mat & reformer) dengan kelas kecil dan booking online, untuk semua bentuk tubuh. Proyek portfolio Ari Zain, concept project No. 02.

**Semua data, nama, dan harga fiktif. Pembayaran disimulasikan; tidak ada integrasi pembayaran atau WhatsApp.**

**Demo:** https://lentur.arizain.my.id (English: [/en](https://lentur.arizain.my.id/en)). Data diulang setiap malam pukul 03.00 WIB.

## Apa yang ada di sini

- **Landing** (`/`, English di `/en`): hero dengan kata "lentur" yang bisa ditarik lalu memantul, ilustrasi tubuh beragam yang meregang saat scroll, "Kamu yang mana?", jadwal 7 hari ke depan, harga jelas + aturan batal, instruktur, dan "Ajak teman".
- **Booking 4 langkah** (`/booking`): jadwal (kelas penuh tetap tampil dengan saran jam lain) → kelas & paket (Kelas Pertama paling atas) → kenalan (nama panggilan, WhatsApp, catatan cedera opsional) → konfirmasi (simpan ke kalender, ajak teman). Peserta bisa membatalkan sendiri lewat tautan di halaman konfirmasi.
- **Admin** (`/admin`, Filament): jadwal mingguan dengan keterisian, daftar peserta bertanda "Pertama kali" dan "Ada catatan", member & paket (sisa kelas), instruktur, jenis kelas.
- **Gerak elastis** dengan pegas fisik (`resources/js/lib/motion.ts`) yang menghormati `prefers-reduced-motion`: tanpa pantulan, cukup fade. Pratinjau komponen di `/playground`.

## Stack

Laravel 13 · Inertia + React 19 (TypeScript) · Tailwind CSS 4 · Filament 5 · SQLite

## Menjalankan secara lokal

Butuh PHP 8.4+, Composer, dan Node 22+.

```bash
composer setup
touch database/database.sqlite
php artisan migrate:fresh --seed
composer run dev
```

Buka http://localhost:8000. Admin ada di http://localhost:8000/admin dengan akun demo `admin@lentur.test` / `lentur-demo` (sama di demo live).

Jadwal demo dibuat relatif terhadap hari seeder dijalankan (minggu ini + minggu depan), jadi jalankan ulang `php artisan migrate:fresh --seed` kalau jadwalnya sudah lewat.

## Pengecekan

```bash
composer ci:check
```

Menjalankan lint & format frontend, TypeScript, Pint, Larastan (level 7), dan semua tes.

## Struktur singkat

| Bagian                                              | Lokasi                                       |
| --------------------------------------------------- | -------------------------------------------- |
| Logika booking (daftar, batal, beli paket simulasi) | `app/Actions`                                |
| Situs publik (landing, booking)                     | `app/Http/Controllers`, `resources/js/pages` |
| Komponen merek (kata elastis, ilustrasi tubuh)      | `resources/js/components/lentur`             |
| Copy Indonesia & Inggris                            | `resources/js/lib/i18n.ts`, `lang/en.json`   |
| Admin                                               | `app/Filament`                               |
| Data demo                                           | `database/seeders`                           |

Panduan merek, nada copy, dan catatan teknis lengkap ada di [`CLAUDE.md`](CLAUDE.md); cara rilis di [`DEPLOY.md`](DEPLOY.md).
