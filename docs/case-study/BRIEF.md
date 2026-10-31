# Brief case study: Lentur (No. 02)

Bahan untuk case study Lentur di arizain.my.id, disusun mengikuti tipe
`CaseStudy` (`resources/js/content/case-studies/types.ts` di repo Ari Zain) dan
gaya case study Alon Cake. Semua fakta di bawah sesuai dengan yang sudah
dibangun dan live; copy hanya usulan, silakan disunting.

- Demo: https://lentur.arizain.my.id (English: `/en`), data direset tiap malam 03.00 WIB
- Repo: https://github.com/codebyaria/lentur-pilates (publik)
- Gambar: folder `images/` di sebelah file ini (lihat bagian "Gambar")
- Sumber brand: `CLAUDE.md` di repo Lentur (Brand Foundation & style tile asli hanya bisa dibuka Aria)

## Data inti

| Field             | Isi                                                              |
| ----------------- | ---------------------------------------------------------------- |
| `slug`            | `lentur`                                                         |
| `no`              | `2`                                                              |
| `name`            | Lentur                                                           |
| `tagline`         | Gerak dulu. Sempurna belakangan.                                 |
| `meta.type`       | Concept project                                                  |
| `meta.foundation` | Bermain                                                          |
| `meta.scope`      | Brand Foundation, arah visual, website, booking 4 langkah, admin |
| `meta.year`       | 2026                                                             |
| `meta.link`       | `lentur.arizain.my.id` → https://lentur.arizain.my.id            |
| `opening.tags`    | Riang · Terbuka · Ringan                                         |

**intro (usulan):** Studio pilates kecil yang percaya bergerak seharusnya
terasa seperti bermain, untuk semua bentuk tubuh. Dari keyakinan itu lahir
sebuah website yang bisa ditarik-tarik, dan sistem booking yang tidak pernah
membuat orang merasa kurang.

**opening.statement:** Bergerak seharusnya terasa seperti bermain.

**opening.detail:** Kelas kecil, mat dan reformer, dengan instruktur yang
lebih mirip teman yang mengajak jalan sore daripada pelatih yang berteriak.

## 1 — Brand & filosofinya

**statement (usulan):** Dunia kebugaran sering dijual lewat rasa kurang: foto
sebelum–sesudah, hitungan kalori, dan teriakan "satu lagi!". Banyak orang
berhenti sebelum sempat mulai.

**body (usulan, 2 paragraf):**

1. Lentur berangkat dari arah sebaliknya: gerak dulu, sempurna belakangan.
   Namanya berarti lentur, mudah ditekuk, dan itu juga cara studionya
   memperlakukan orang: kelas kecil, level yang bisa diganti kapan saja, dan
   tidak ada tubuh yang dijadikan contoh.
2. Semua itu ditulis dulu dalam Brand Foundation sebelum warna pertama dipilih.
   Dari sana lahir tiga nilai yang menguji setiap keputusan.

**values:**

| Nilai   | body                                                             |
| ------- | ---------------------------------------------------------------- |
| Riang   | Bergerak itu bermain. Ada tawa, ada pantulan, tidak ada hukuman. |
| Terbuka | Untuk semua bentuk tubuh, pertama kali atau sudah lengket.       |
| Ringan  | Kalimat pendek, harga jelas, batal tanpa drama.                  |

## 2 — Terjemahan

**statement (usulan):** Nilai diturunkan dulu jadi kriteria visual. Referensi
fitness yang keren tapi menuntut, dibuang.

**criteria:**

| value   | criterion                                                                        |
| ------- | -------------------------------------------------------------------------------- |
| Riang   | Elemen memanjang lalu memantul; limau dan merah muda di atas putih hangat        |
| Terbuka | Ilustrasi tubuh beragam (termasuk pengguna kursi roda), bukan foto tubuh "ideal" |
| Ringan  | Sudut membulat besar, chip berbentuk pil, copy yang sedikit bercanda             |

**media:** `style.jpg` (palet warna), `type.jpg` (tipografi). Lihat "Gambar".

**decisions** (`title` / `detail` / `why`):

1. **Empat warna, satu aturan** / Putih hangat `#FFF8EE`, tinta biru
   `#1F2A44`, limau `#C8F04B`, merah muda `#FF9EC4`, biru `#2F5BE8`. / Riang
   tanpa mengorbankan keterbacaan: teks tinta di atas limau dan merah muda,
   putih hanya di atas biru. Tombol utama sengaja dikunci ke biru persis, bahkan
   di admin.
2. **Bricolage Grotesque + Figtree** / Bricolage hanya untuk judul (700–800),
   Figtree untuk teks, tombol, dan form. / Bricolage terasa hidup dan sedikit
   nakal; Figtree tetap jelas dibaca di form booking.
3. **Ilustrasi, bukan foto** / Kepala bulat dan badan pil dalam enam ukuran,
   termasuk pengguna kursi roda. / Tidak ada satu tubuh pun yang jadi standar.
   Ilustrasi juga ikut meregang saat halaman di-scroll.
4. **Kata yang bisa ditarik** / Hero dipimpin gerak, bukan kalimat besar: kata
   "lentur" bisa ditarik kursor atau diusap, lalu memantul pulang. / Filosofi
   ditunjukkan dalam detik pertama, bukan diceritakan. Huruf memanjang menjauhi
   tetangganya supaya tetap terbaca.
5. **Pegas, bukan timer** / Gerak elastis memakai pegas fisik (~700 ms), sama
   rasanya di semua elemen. / Pantulan terasa seperti main. Pengguna yang
   memilih mengurangi animasi mendapat fade saja, tanpa pantulan.
6. **Level sebagai pertemanan** / _Baru Kenal · Mulai Akrab · Sudah Lengket._ /
   Level bukan peringkat. Tidak ada "pemula" atau "advanced" yang membuat orang
   merasa tertinggal.

## 3 — Sistem

**statement (usulan):** Di checkout, kebanyakan studio berubah jadi mesin
tekanan: akun wajib, paket tahunan, dan jadwal penuh tanpa jalan keluar.

**body (usulan):** Booking adalah tempat filosofi paling mudah hilang. Jadi
setiap langkahnya diuji dengan pertanyaan yang sama: apakah ini terasa seperti
teman yang mengajak, atau pelatih yang menuntut?

**flow** (empat langkah, screenshot ponsel):

| label            | file         | alt                                                                                                                       |
| ---------------- | ------------ | ------------------------------------------------------------------------------------------------------------------------- |
| `[1 Jadwal]`     | `flow-1.jpg` | Langkah 1: kelas Kamis 18:30 penuh, dengan pesan "Kelas ini sudah ramai. Jam berikutnya masih lega." dan tautan ke 19:45. |
| `[2 Paket]`      | `flow-2.jpg` | Langkah 2: pilihan paket dengan Kelas Pertama Rp 99.000 paling atas dan ringkasan kelas di bawahnya.                      |
| `[3 Kenalan]`    | `flow-3.jpg` | Langkah 3: nama panggilan, nomor WhatsApp, pilihan "Iya, pertama kali", dan catatan cedera opsional.                      |
| `[4 Konfirmasi]` | `flow-4.jpg` | Langkah 4: "Sampai ketemu Kamis! Bawa air minum, tinggalkan rasa sungkan." dengan enam ilustrasi tubuh.                   |

**features** (`title` / `body` / `why`):

1. **Kelas penuh tidak disembunyikan** / Kelas penuh tetap tampil, lengkap
   dengan jam lain yang masih lega (jenis kelas sama didahulukan). / Tidak ada
   jalan buntu, dan tidak ada "habis!" yang membuat panik.
2. **Kelas pertama paling atas** / Paket Kelas Pertama Rp 99.000 selalu di
   urutan pertama, sekali per orang. / Pintu masuk termurah adalah yang paling
   mudah ditemukan.
3. **Kenalan, bukan registrasi** / Cukup nama panggilan dan WhatsApp. Tanpa
   akun, tanpa password. Ada pertanyaan "Pertama kali pilates?" dan catatan
   cedera opsional. / Yang ditanyakan hanya yang membantu instruktur menyambut.
4. **Konfirmasi yang menenangkan** / _"Sampai ketemu Kamis! Bawa air minum,
   tinggalkan rasa sungkan."_ Hari kelasnya bisa ditarik seperti di hero. Ada
   tombol simpan ke kalender dan ajak teman. / Penutup yang mengurangi
   gugup, bukan menambah daftar tugas.
5. **Batal tanpa drama** / Peserta bisa membatalkan sendiri lewat tautan di
   halaman konfirmasi. Sebelum 12 jam: kelas kembali ke paket. Sesudahnya:
   tempat dibuka untuk orang lain, kelas tetap terhitung. / Aturan yang sama
   ditulis jelas di halaman harga, tanpa huruf kecil.
6. **Admin yang menyiapkan instruktur** / Jadwal mingguan dengan keterisian,
   dan daftar peserta bertanda "Pertama kali" dan "Ada catatan". / Instruktur
   tahu siapa yang perlu disambut lebih hangat dan lutut siapa yang perlu
   dijaga, sebelum kelas mulai.
7. **Yang sengaja tidak ada** / Foto sebelum–sesudah, hitungan kalori, teriakan
   "satu lagi!", cermin di setiap dinding, kontrak tahunan, biaya tersembunyi.
   Semua disebut terang-terangan di landing. / Yang ditinggalkan sama
   pentingnya dengan yang dibangun.

**media:** `elastic.jpg` (kata "lentur" sedang ditarik), `admin.jpg` (jadwal
mingguan), `admin-peserta.jpg` (peserta dengan tanda "Pertama kali" dan "Ada
catatan").

## 4 — Hasil

**statement (usulan):** Satu website yang bisa diajak bermain, dan sistem
booking yang tidak pernah membuat orang merasa kurang.

**points (fakta):**

- Landing dengan hero elastis, jadwal 7 hari, harga jelas, dan aturan batal
- Booking 4 langkah tanpa akun, plus batal sendiri dan simpan ke kalender
- Admin Filament: jadwal mingguan, peserta bertanda, member & sisa kelas
- Bahasa Indonesia dan Inggris, termasuk isi dari database
- Menghormati `prefers-reduced-motion`; diuji di layar ponsel 390px
- 62 tes otomatis; demo live direset tiap malam

**cta:** `Lihat Lentur` → https://lentur.arizain.my.id

**disclaimer (usulan):** Proyek konsep untuk portofolio. Studio, instruktur,
member, dan harga fiktif; pembayaran disimulasikan.

## 5 — Refleksi (draf, perlu suara Aria)

Bagian ini paling personal; draf di bawah hanya titik awal.

1. Gerak yang riang ternyata mudah berubah jadi gangguan. Pegas yang sama harus
   terasa main di hero, tapi tenang di form booking, dan hilang sama sekali bagi
   yang memilih mengurangi animasi.
2. Aturan kontras ("putih hanya di atas biru") terdengar membatasi, tapi justru
   membuat palet yang ramai tetap terbaca, sampai ke tombol di panel admin.
3. Hal yang paling banyak mengubah desain bukan tampilan, tapi pertanyaan:
   apa yang perlu diketahui instruktur sebelum kelas? Dari situ lahir tanda
   "Pertama kali" dan "Ada catatan".

## Gambar

Semua di `images/`, mengikuti pola `image()` di `media.ts` (file terbesar =
nama asli, varian `-{lebar}.jpg`). Salin ke `public/images/lentur/`.

| File                      | Ukuran    | Varian          | Isi / alt                                                                                                                                |
| ------------------------- | --------- | --------------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| `hero.jpg`                | 2000×1250 | `-1200`, `-800` | Halaman depan Lentur: kata "lentur" besar berwarna biru, "Gerak dulu. Sempurna belakangan.", dan enam ilustrasi tubuh beragam.           |
| `elastic.jpg`             | 2000×800  | `-1000`         | Kata "lentur" sedang ditarik ke kanan: huruf-hurufnya memanjang dan merenggang.                                                          |
| `style.jpg`               | 1600×306  | `-800`          | Palet Lentur: putih hangat, tinta biru, limau, merah muda, dan biru, masing-masing dengan aturan teksnya.                                |
| `type.jpg`                | 1000×866  | `-600`          | "Gerak dulu. Sempurna belakangan." dalam Bricolage Grotesque 800, dan "Kelas ini sudah ramai. Jam berikutnya masih lega." dalam Figtree. |
| `flow-1.jpg`…`flow-4.jpg` | 780×1688  | `-400`          | Lihat tabel flow di atas.                                                                                                                |
| `admin.jpg`               | 2000×1250 | `-1000`         | Admin: jadwal mingguan Senin–Minggu dengan bar keterisian, label "Penuh", "pertama kali", dan "ada catatan".                             |
| `admin-peserta.jpg`       | 2000×1250 | `-1000`         | Admin: peserta kelas Reformer Main-main; Nindy bertanda "Pertama kali" dan "Ada catatan" dengan catatan lututnya.                        |

Gambar pratinjau tautan Lentur sendiri ada di repo Lentur:
`public/images/og/lentur-id.png` dan `lentur-en.png` (1200×630), kalau berguna
untuk kartu di halaman arsip.
