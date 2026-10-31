# Lentur — demo portfolio Ari Zain (concept project No. 02)

Studio pilates (mat & reformer), kelas kecil, booking online.
Filosofi: bergerak seharusnya terasa seperti bermain, untuk semua bentuk tubuh.
Nilai: riang, terbuka, ringan. Karakter: teman yang mengajak jalan sore, bukan pelatih yang berteriak.

## Stack

Laravel + Inertia React (TypeScript) + Tailwind + Filament (admin).
Demo tanpa integrasi: pembayaran disimulasikan, data dari seeder.
Bahasa: Indonesia (default) + English (/en).

## Visual

- Warna: putih hangat #FFF8EE (dasar), tinta biru #1F2A44 (teks), limau #C8F04B, merah muda #FF9EC4, biru #2F5BE8 (tombol utama)
- Kontras: teks tinta biru di atas limau/merah muda; putih HANYA di atas biru
- Font: Bricolage Grotesque (judul saja, 700–800), Figtree (body, tombol, form)
- Bentuk: sudut membulat besar (24–48px), chip berbentuk pil, ilustrasi tubuh beragam (kepala bulat + badan pil berbagai ukuran)
- Hero dipimpin gerak, bukan kalimat besar

## Interaksi

- Ciri khas elastis: elemen memanjang lalu memantul (spring, cubic-bezier(0.34, 1.56, 0.64, 1), ~700 ms)
- Hero: kata "lentur" bisa ditarik kursor/diusap lalu memantul; ilustrasi tubuh sedikit meregang saat scroll
- Hormati prefers-reduced-motion (tanpa pantulan, cukup fade)

## Copy

- Hangat, sedikit bercanda, kalimat pendek, banyak kata kerja
- Hero: "Gerak dulu. Sempurna belakangan."
- Kelas penuh: "Kelas ini sudah ramai. Jam berikutnya masih lega."
- Konfirmasi: "Sampai ketemu Kamis! Bawa air minum, tinggalkan rasa sungkan."
- Level: Baru Kenal · Mulai Akrab · Sudah Lengket
- DILARANG: foto before-after, "bakar kalori", "body goals", nada menuntut

## Halaman

- Landing: hero elastis, "Kamu yang mana?", jadwal minggu ini, harga jelas + aturan batal, "Yang tidak akan kamu temukan di sini", instruktur, "Ajak teman, gerak bareng"
- Booking (4 langkah): jadwal (filter level, kelas penuh tetap tampil + saran jam lain) -> kelas & paket (kelas pertama paling atas) -> kenalan (nama panggilan, WhatsApp, "Pertama kali pilates?", catatan cedera opsional) -> konfirmasi (ajak teman, simpan ke kalender)
- Admin (Filament): jadwal mingguan + keterisian, daftar peserta dengan tanda "Pertama kali" dan "Ada catatan", member & paket (sisa kelas), instruktur

## Referensi (hanya bisa dibuka Aria)

- Brand Foundation: https://claude.ai/artifact/WFKHzWbjtfiQnb72NYSUuW
- Style tile & wireframe: https://claude.ai/artifact/U4dhAhjqekvBpQYhV1S57X

## Catatan teknis

- Jalankan: `composer run dev` (server Laravel + Vite) → http://localhost:8000
- Reset data: `php artisan migrate:fresh --seed` (akun admin ada di `database/seeders/DatabaseSeeder.php`)
- Akses admin Filament dibatasi kolom `users.is_admin`
- Design tokens: `resources/css/app.css` (`@theme`, kelas `bg-lentur-*`, `font-display`)
- Gerak: `resources/js/lib/motion.ts` — semua animasi pegas lewat sini, selalu cek `prefersReducedMotion()`
- Komponen merek: `resources/js/components/lentur/` — pratinjau di `/playground`
- Data demo: `StudioSeeder` (instruktur, jenis kelas, paket), `ScheduleSeeder` (jadwal minggu ini + depan, relatif hari seeder dijalankan; slot sengaja penuh di `FULL_SLOTS`), `MemberSeeder` (member fiktif, paket, booking; acak tapi deterministik)
- Aturan batal gratis: `config/lentur.php` (`free_cancellation_hours`) → `Booking::canCancelForFree()`
- Kelas penuh → `ClassSession::nextAvailableAlternative()` (jenis kelas sama didahulukan, level sama, 7 hari ke depan)
- Tanggal = `CarbonImmutable`, zona waktu Asia/Jakarta. Teks UI ditulis dalam bahasa Indonesia; terjemahan Inggris di `lang/en.json`
