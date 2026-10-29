# Portal Web App Wibindo

Landing page internal untuk mengumpulkan seluruh web app perusahaan dalam satu tempat. Dibangun dengan PHP native tanpa framework, database, login, panel admin, atau CMS.

## Stack

- PHP native minimal 8.4 (target PHP 8.5), `declare(strict_types=1)` di semua file PHP.
- HTML semantik, CSS lokal, dan vanilla JavaScript.
- Tidak ada CDN, font eksternal, request pihak ketiga, atau dependency runtime.
- Konten aplikasi hanya dikelola dari `config/apps.php`.

## Struktur Proyek

```text
.
├── public/                     # document root
│   ├── index.php
│   └── assets/
│       ├── css/app.css
│       ├── js/app.js
│       ├── fonts/              # kosong; UI memakai font sistem
│       └── img/
│           ├── favicon.svg
│           ├── logo.svg
│           └── screenshots/
│               └── placeholder.webp
├── config/apps.php             # sumber data 8 aplikasi
├── src/
│   ├── App.php
│   ├── Category.php
│   └── helpers.php
├── templates/
│   ├── layout.php
│   └── partials/
│       ├── app-card.php
│       ├── footer.php
│       ├── header.php
│       └── hero.php
├── tools/                      # panduan screenshot (opsional)
├── legacy-nextjs/              # proyek Next.js lama, disimpan sementara
├── blueprint-landing-page-web-app.md
└── README.md
```

Hanya folder `public/` yang boleh dijadikan document root.

## Menjalankan Lokal

Gunakan PHP 8.4 atau 8.5. Contoh dengan built-in server:

```bash
php -S localhost:8000 -t public
```

Buka `http://localhost:8000`.

Jika `mbstring` belum aktif, jalankan dengan extension tersebut:

```bash
php -d extension=mbstring -S localhost:8000 -t public
```

Catatan: mesin pengembangan saat ini memiliki PHP 8.0 di `C:\xampp`. Versi tersebut tidak dapat menjalankan `enum` dan `readonly class`; gunakan PHP 8.4+ atau portable PHP 8.5 sebelum menjalankan aplikasi.

## Menambah atau Mengubah Aplikasi

1. Tambahkan objek `new App(...)` di `config/apps.php`.
2. Letakkan screenshot di `public/assets/img/screenshots/<slug>.webp`.
3. Jika screenshot belum tersedia, biarkan nama file di konfigurasi; helper `screenshot()` otomatis memakai `placeholder.webp`.

Field `screenshot` berisi nama file, misalnya `livo.webp`. Kategori memakai enum `Category` di `src/Category.php`.

## Screenshot

- Ukuran 1280 x 800 px, rasio 16:10, format WebP kualitas 80, target di bawah 150 KB per file.
- Gunakan data dummy atau data yang sudah disamarkan.
- Jangan menampilkan kredensial, token, atau data pelanggan.
- Lihat `tools/README.md` untuk alur pengambilan screenshot.

## Kebutuhan Runtime dan Deployment

Runbook deployment internal lengkap tersedia di [DEPLOYMENT.md](DEPLOYMENT.md). Target deployment adalah `portal.wibindo.local` melalui Caddy `192.168.31.52` ke LXC Ubuntu `192.168.31.58`, dengan document root `/var/www/portal/public`.

Kebutuhan server mengikuti blueprint bagian 7.1-7.5:

- Ubuntu 24.04 di LXC Proxmox, Nginx + PHP-FPM, document root `/var/www/portal/public`.
- PHP 8.5 dari PPA `ondrej/php` (minimal 8.4), termasuk `php8.5-fpm`, `php8.5-cli`, dan `php8.5-mbstring`.
- `expose_php = Off`, `display_errors = Off`, `log_errors = On`, OPcache aktif.
- Nginx hanya listen HTTP port 80 dan menerima koneksi dari Caddy `192.168.31.52`.
- Caddy melakukan terminasi TLS dan membatasi akses dengan allowlist `remote_ip`.
- Header keamanan (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, CSP) dikirim oleh `public/index.php`; Nginx dan Caddy perlu menerapkan defense-in-depth sesuai runbook.
- Tidak ada langkah build di server. Cukup deploy source dan pastikan `public/` menjadi document root.

### Langkah Deployment Ringkas

1. Siapkan LXC Ubuntu 24.04 sesuai blueprint bagian 7.2 dan pasang `nginx`, `php8.5-fpm`, `php8.5-cli`, serta `php8.5-mbstring`.
2. Deploy repository ke `/var/www/portal` dan pastikan hanya `public/` yang menjadi document root.
3. Pasang konfigurasi Nginx dari blueprint bagian 7.3, lalu `nginx -t` dan reload.
4. Pasang blok Caddy dari blueprint bagian 7.4 di `192.168.31.52`, lalu validasi dan reload Caddy.
5. Terapkan pengaturan PHP dari blueprint bagian 7.5 dan restart PHP-FPM.
6. Verifikasi dengan tabel pengecekan blueprint bagian 7.7.

Perubahan Nginx, Caddy, DNS, firewall, dan server produksi adalah tanggung jawab partner DevOps. Repository ini tidak mengubah konfigurasi server.

## Keamanan dan SEO

- `<meta name="robots" content="noindex, nofollow">`.
- Semua output dinamis di-escape dengan `e()`.
- Tidak ada input yang diproses di server.
- Link aplikasi memakai `target="_blank"` dan `rel="noopener noreferrer"`.
- Folder `config/`, `src/`, dan `templates/` berada di luar document root.

## Validasi

```bash
find . -name '*.php' -not -path './legacy-nextjs/*' -print0 | xargs -0 -n1 php -l
```

Audit lokal 2026-10-28: 10/10 file PHP lolos `php -l` pada PHP 8.5.12, sintaks JavaScript lolos `node --check`, root HTTP menghasilkan delapan kartu, dan aset CSS/JS/logo/favicon/placeholder merespons 200. Pencarian, filter Produksi, kondisi hasil kosong, reset pencarian, tautan kartu, dan tampilan portal lokal diverifikasi secara interaktif. CSS memiliki breakpoint mobile, tablet, desktop, dan wide-screen; uji jaringan produksi, console browser penuh, dan verifikasi responsive viewport tambahan tetap menjadi bagian serah-terima DevOps/QA. Mesin pengembangan default dengan PHP 8.0 belum dapat menjalankan aplikasi.

Portal ini tidak memakai Composer, database, maupun build step.

## Placeholder yang Perlu Diganti

- Logo pada `public/assets/img/logo.svg` dan `public/assets/img/favicon.svg` masih placeholder.
- Kontak IT di footer memakai `it@wibindo.local` (variabel `$itContactEmail` di `public/index.php`).
- Screenshot asli belum tersedia; saat ini seluruh kartu memakai `placeholder.webp`.

## Proyek Lama

Proyek Next.js sebelumnya dipindahkan ke `legacy-nextjs/` dan tidak dihapus. Folder tersebut tidak termasuk document root portal dan tetap dipertahankan sesuai instruksi proyek.
