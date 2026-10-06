# Panduan Konfigurasi cPanel: Subdomain Peta Resistensi (PHP 8.3 + PostgreSQL)

Panduan ini untuk menjalankan **Laravel 13 + Filament v5** di cPanel dengan **PHP 8.3** dan **PostgreSQL**, tanpa mengubah aplikasi lain di akun yang sama.

> Ganti `USER` dengan username cPanel, `namadomain.go.id` dengan domain Anda, dan `resistensi` dengan nama subdomain yang dipilih.

---

## 0. Cek dulu: versi PHP berlaku per subdomain atau per akun?

Tangkapan layar yang Anda kirim (ada opsi `native (7.4)`) mirip menu **Select PHP Version** milik **CloudLinux**. Di menu itu, versi PHP bisa berlaku untuk **seluruh akun**, bukan per domain. Sebelum mengubah apa pun:

1. Buka cPanel → **MultiPHP Manager**.
2. Lihat tabel domain di bagian bawah. Jika **setiap domain/subdomain punya dropdown versi PHP sendiri** (misalnya `ea-php83` atau `alt-php83`), pengaturan per subdomain bisa dipakai. Lanjut ke langkah 1.
3. Jika tidak ada dropdown per domain, atau versi hanya bisa diubah di **Select PHP Version** untuk seluruh akun, **jangan ubah dulu**. Pilihannya:
   - minta pengelola hosting mengaktifkan PHP 8.3 khusus subdomain ini;
   - buat **akun cPanel terpisah** untuk sistem ini (paling terisolasi);
   - atau uji semua aplikasi lama di PHP 8.3 di lingkungan uji, baru naikkan satu akun.

---

## 1. Buat subdomain

cPanel → **Domains** (atau **Subdomains**) → **Create A New Domain**:

| Isian | Nilai |
|-------|-------|
| Domain | `resistensi.namadomain.go.id` |
| Share document root | **Hilangkan centang** |
| Document Root | `/home/USER/resistensi/public` |

Struktur folder yang dipakai:

```
/home/USER/resistensi/          <- seluruh kode Laravel (di luar public_html)
/home/USER/resistensi/public/   <- document root subdomain
```

Jangan menaruh kode Laravel di dalam `public_html`. Document root harus mengarah ke folder `public` agar `.env`, `vendor`, dan `storage` tidak bisa diakses dari web.

## 2. SSL dan HTTPS

1. cPanel → **SSL/TLS Status** → centang subdomain → **Run AutoSSL**.
2. cPanel → **Domains** → aktifkan **Force HTTPS Redirect** untuk subdomain.

## 3. Atur versi PHP 8.3 (setelah lolos langkah 0)

1. cPanel → **MultiPHP Manager** → centang **hanya** subdomain baru.
2. Pilih **PHP 8.3** → **Apply**.
3. Bila tersedia, aktifkan **PHP-FPM** untuk subdomain.

## 4. Ekstensi PHP 8.3

Aktifkan lewat **Select PHP Version** (tab *Extensions*) untuk versi 8.3:

| Ekstensi | Keterangan |
|----------|------------|
| `pdo_pgsql`, `pgsql` | Koneksi PostgreSQL |
| `mbstring`, `xml`, `dom`, `curl`, `fileinfo`, `openssl`, `tokenizer`, `ctype` | Kebutuhan dasar Laravel |
| `intl` | Kebutuhan Filament |
| `zip` | Ekspor/impor Excel |
| `gd`, `exif` | Pengolahan gambar upload |
| `bcmath` | Perhitungan angka |
| `opcache` | Kinerja |

## 5. Opsi PHP (INI)

Di **MultiPHP INI Editor** (atau tab *Options* di Select PHP Version), untuk subdomain ini:

| Opsi | Nilai disarankan |
|------|------------------|
| `memory_limit` | `512M` (minimal `256M`) |
| `max_execution_time` | `300` |
| `max_input_time` | `300` |
| `upload_max_filesize` | `20M` |
| `post_max_size` | `25M` |
| `max_input_vars` | `3000` |
| `display_errors` | `Off` |
| `date.timezone` | `Asia/Jakarta` |
| `opcache.enable` | `1` |

Naikkan `memory_limit` dan `max_execution_time` bila impor Excel atau ekspor besar kena batas. Sebagian nilai bisa dibatasi oleh hosting.

## 6. Database PostgreSQL

cPanel → **PostgreSQL Databases**:

1. **Create New Database**: misalnya `resistensi` (menjadi `USER_resistensi`).
2. **Add New User**: misalnya `app` (menjadi `USER_app`) dengan kata sandi kuat.
3. **Add User To Database**: pilih user dan database, beri **ALL PRIVILEGES**.

Catatan:
- Host `127.0.0.1`, port `5432`. Akses remote tidak perlu diaktifkan.
- Ekstensi seperti `pg_trgm` (untuk pencarian teks) dan PostGIS bersifat **opsional**. Pembuatannya bisa memerlukan hak khusus, jadi tanyakan ke pengelola hosting bila dibutuhkan. PRD tidak mewajibkan keduanya.

## 7. Unggah aplikasi

Cara A (disarankan, bila Terminal/SSH tersedia):

```bash
cd ~
git clone <alamat-repositori> resistensi      # atau unggah zip lewat File Manager
cd resistensi
```

Cara B (tanpa SSH): jalankan `composer install` dan `npm run build` di komputer lokal, lalu unggah seluruh folder termasuk `vendor` dan `public/build`.

## 8. Gunakan PHP 8.3 di Terminal

Terminal sering memakai PHP bawaan server, bukan versi subdomain. Cek:

```bash
php -v
```

Bila bukan 8.3, panggil langsung (salah satu jalur berikut, tergantung server):

```bash
/opt/cpanel/ea-php83/root/usr/bin/php -v
/opt/alt/php83/usr/bin/php -v
```

Untuk mempermudah, buat alias di `~/.bashrc`:

```bash
alias php83='/opt/cpanel/ea-php83/root/usr/bin/php'   # sesuaikan jalur
alias composer83='php83 $(which composer)'
```

## 9. File `.env`

Salin `.env.example` menjadi `.env`, lalu isi:

```dotenv
APP_NAME="Peta Resistensi"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://resistensi.namadomain.go.id
APP_LOCALE=id
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=daily
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=USER_resistensi
DB_USERNAME=USER_app
DB_PASSWORD=isi_kata_sandi

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...

# Peta (lihat bagian Keputusan Peta di PRD)
MAP_TILE_URL=https://tile.openstreetmap.org/{z}/{x}/{y}.png
MAP_TILE_ATTRIBUTION="&copy; OpenStreetMap contributors"
```

Ubah juga `timezone` di `config/app.php` menjadi `Asia/Jakarta`. Saat produksi, ganti `MAP_TILE_URL` ke penyedia tile produksi.

## 10. Perintah instalasi

```bash
cd ~/resistensi
php83 $(which composer) install --no-dev --optimize-autoloader
php83 artisan key:generate
php83 artisan migrate --force
php83 artisan db:seed --force          # seeder master data dan wilayah
php83 artisan storage:link
php83 artisan config:cache
php83 artisan route:cache
php83 artisan view:cache
php83 artisan filament:optimize
```

Izin folder: `storage` dan `bootstrap/cache` harus dapat ditulis oleh user cPanel (umumnya `755`, bukan `777`):

```bash
chmod -R 755 storage bootstrap/cache
```

Bila halaman error setelah mengubah `.env`, jalankan `php83 artisan config:clear` lalu `config:cache` lagi.

## 11. Cron job

cPanel → **Cron Jobs** (sesuaikan jalur PHP):

| Fungsi | Jadwal | Perintah |
|--------|--------|----------|
| Scheduler Laravel | Setiap menit | `* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/USER/resistensi/artisan schedule:run >> /dev/null 2>&1` |
| Antrean (tanpa proses permanen) | Setiap menit | `* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/USER/resistensi/artisan queue:work --stop-when-empty --max-time=55 >> /dev/null 2>&1` |
| Backup database | Harian 02:00 | `0 2 * * * pg_dump -h 127.0.0.1 -U USER_app USER_resistensi \| gzip > /home/USER/backups/resistensi_$(date +\%F).sql.gz` |

Untuk `pg_dump` tanpa memasukkan kata sandi, buat `~/.pgpass`:

```
127.0.0.1:5432:USER_resistensi:USER_app:isi_kata_sandi
```

lalu `chmod 600 ~/.pgpass`. Buat folder `~/backups`, dan tambahkan backup folder `storage/app` (file upload) serta pembersihan backup lama.

## 12. Pengamanan dasar

- `APP_DEBUG=false` di produksi. Jangan pernah mengaktifkan `true`.
- Pastikan `.env` tidak bisa diakses lewat web (aman bila document root = `public`).
- Uji: buka `https://resistensi.namadomain.go.id/.env`. Hasilnya harus 404 atau 403.
- Batasi akses panel `/admin` bila memungkinkan (verifikasi dua langkah untuk Super Admin dan Admin, sesuai PRD).
- Aktifkan backup otomatis cPanel (bila ada) selain cron di atas.

## 13. Pengecekan akhir

| Cek | Cara | Hasil yang benar |
|-----|------|------------------|
| Versi PHP subdomain | `php83 artisan about` atau file `phpinfo()` sementara (hapus setelah dicek) | PHP 8.3.x |
| Aplikasi lama tidak terpengaruh | Buka aplikasi lama dan cek `phpinfo()` masing-masing | Versi PHP tidak berubah |
| Koneksi database | `php83 artisan migrate:status` | Tidak ada error koneksi |
| Peta dan filter | Buka Peta Sebaran | Tile tampil, titik dimuat |
| Upload dan ekspor | Unggah dokumen, ekspor Excel/PDF | Berhasil tanpa timeout |
| Cron | Lihat `storage/logs` setelah beberapa menit | Tidak ada error scheduler |
