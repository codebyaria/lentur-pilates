# Deploy lentur.arizain.my.id

Demo ini berjalan di VPS yang sama dengan arizain.my.id dan
aloncake.arizain.my.id, dibuka ke publik lewat Cloudflare Tunnel. Tidak ada
port yang dibuka di server.

## Rilis

```bash
scripts/deploy.sh
```

Skrip itu mengarsipkan commit `HEAD`, mem-build frontend di Mac ini, mengunggah
keduanya, lalu menjalankan `composer install`, migrasi, dan `php artisan optimize`
di server. Working tree harus bersih. Butuh host `alon-vps` di `~/.ssh/config`.

## Setup server (sudah terpasang)

| Bagian   | Isi                                                                                  |
| -------- | ------------------------------------------------------------------------------------ |
| Server   | VPS NAT Ubuntu 24.04, 1 GB RAM, SSH `alon-vps`                                       |
| Aplikasi | `/var/www/lentur`, PHP 8.5-FPM, SQLite (`database/database.sqlite`)                  |
| Nginx    | `/etc/nginx/sites-available/lentur`, listen `127.0.0.1:8082`                         |
| Tunnel   | `/etc/cloudflared/config.yml`: `lentur.arizain.my.id` → `127.0.0.1:8082`             |
| DNS      | CNAME `lentur.arizain.my.id` ke tunnel, dibuat dengan `cloudflared tunnel route dns` |
| Reset    | `/etc/cron.d/lentur` menjalankan `schedule:run`; data diulang tiap malam 03.00 WIB   |

Port yang sudah dipakai di server ini: Alon Cake `8080`, Ari Zain `8081`, Lentur `8082`.

## `.env` produksi

```dotenv
APP_NAME=Lentur
APP_ENV=production
APP_DEBUG=false
APP_DEMO=true
APP_URL=https://lentur.arizain.my.id
APP_LOCALE=id
APP_FAKER_LOCALE=id_ID
LOG_LEVEL=warning

DB_CONNECTION=sqlite
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

- `APP_DEMO=true` mengizinkan `migrate:fresh` di produksi dan menyalakan reset
  malam (`routes/console.php`). Jadwal demo dibuat relatif terhadap hari itu.
- Aplikasi mempercayai header proxy (`trustProxies`) dan memaksa https di
  produksi (`URL::forceHttps`); keduanya wajib agar URL bertanda tangan
  (konfirmasi, batal, kalender) valid di balik tunnel.
- `fakerphp/faker` ada di `require` (bukan `require-dev`): seeder dijalankan di server saat reset malam.
- Akun admin demo sengaja publik (lihat README); semua perubahan hilang saat reset.
