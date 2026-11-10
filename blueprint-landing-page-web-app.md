# Blueprint: Landing Page Portal Web App Wibindo

> Dokumen ini ditujukan untuk engineer yang akan membangun landing page portal yang berisi kumpulan link ke seluruh web app internal perusahaan.

| Item | Keterangan |
|---|---|
| Nama proyek | Portal Web App (Landing Page) |
| Tipe | Website statis (tanpa database) |
| Bahasa/Runtime | PHP rilis stabil terbaru (saat ini **PHP 8.5**; gunakan rilis stabil terbaru saat implementasi, minimal 8.4) |
| Bahasa antarmuka | Bahasa Indonesia |
| Sumber data | 1 file konfigurasi PHP (`config/apps.php`) |
| Output | Halaman tunggal (single page) berisi grid kartu web app |

---

## 1. Tujuan & Ruang Lingkup

### Tujuan
Menyediakan **satu pintu masuk** bagi seluruh karyawan untuk menemukan dan membuka web app perusahaan. Setiap web app ditampilkan dengan **screenshot, nama, dan deskripsi** singkat yang menarik, plus tombol menuju aplikasinya.

### In scope
- Satu halaman landing dengan header, grid kartu aplikasi, dan footer.
- Kartu aplikasi: screenshot, nama, kategori, deskripsi, tombol "Buka Aplikasi".
- Filter kategori dan kolom pencarian sisi klien (JavaScript ringan).
- Responsif (mobile, tablet, desktop) dan dukungan dark mode.
- Pengelolaan konten hanya lewat satu file data (`config/apps.php`).

### Out of scope
- Database, login/SSO, panel admin, CMS.
- Pengecekan status uptime real-time (lihat bagian *Pengembangan Lanjutan*).
- Penyimpanan data pengguna apa pun.

---

## 2. Daftar Web App (Konten Final)

Seluruh teks di bawah adalah konten siap pakai. Engineer cukup memindahkannya ke `config/apps.php`.

| # | Slug | Nama | Kategori | URL |
|---|---|---|---|---|
| 1 | `livo` | Livo WMS (Legacy) | Gudang & Logistik | https://livo.wibindo.local/login |
| 2 | `ribbon-production` | Ribbon Production | Produksi | https://ribbon-production.wibindo.local/ |
| 3 | `bulk-production` | Bulk Production | Produksi | https://bulk-production.wibindo.local/productions |
| 4 | `wms` | WMS | Gudang & Logistik | https://wms.wibindo.local/auth/login |
| 5 | `inventory` | Inventory | Aset & Inventaris | https://inventory.wibindo.local/auth/login |
| 6 | `minio` | MinIO Object Store | Infrastruktur & Media | https://minio.wibisana.id/login |
| 7 | `product-images` | Product Images | Infrastruktur & Media | https://product-images.wibisana.id/auth/login |
| 8 | `tomoro-activities` | Tomoro Activities | Operasional Outlet | https://tomoro-activities.wibisana.id/auth/login |

### 2.1 Tagline & Deskripsi

**1. Livo WMS (Legacy)** — *Sistem gudang generasi pertama yang masih setia bekerja.*
Aplikasi warehouse management system lama untuk mencatat barang masuk, keluar, dan posisi stok di gudang. Tetap tersedia untuk kebutuhan data historis dan proses yang belum bermigrasi ke WMS baru.
Badge: `Legacy`

**2. Ribbon Production** — *Dari gulungan besar menjadi pita presisi, tercatat rapi.*
Kelola produksi pemotongan pita dan pantau stok bahan pita dalam satu tempat. Setiap pemotongan tercatat, setiap sisa bahan terpantau, sehingga perencanaan produksi jadi lebih cepat dan akurat.

**3. Bulk Production** — *Produksi borongan pita, terpantau dari awal sampai selesai.*
Catat dan kontrol seluruh proses produksi borongan pita: mulai dari order, progres pengerjaan, hingga hasil akhir. Memudahkan tim produksi menjaga target dan mengurangi pencatatan manual.

**4. WMS** — *Gudang lebih cerdas, stok lebih jujur.*
Generasi terbaru warehouse management system perusahaan. Alur penerimaan, penyimpanan, dan pengeluaran barang dibuat lebih ringkas dengan data stok yang lebih akurat dan mudah ditelusuri.
Badge: `Baru`

**5. Inventory** — *Setiap aset punya catatan, setiap stok opname punya jejak.*
Register inventaris perusahaan sekaligus alat stok opname. Daftarkan aset, cek kondisi dan lokasinya, lalu lakukan pencocokan fisik dengan data sistem tanpa tumpukan kertas.

**6. MinIO Object Store** — *Gudang digital perusahaan untuk file berukuran apa pun.*
Penyimpanan objek (object storage) internal untuk dokumen, backup, dan berkas aplikasi. Fondasi penyimpanan yang cepat, andal, dan kompatibel dengan S3.
Badge: `Untuk tim IT`

**7. Product Images** — *Foto produk rapi, satu sumber untuk semua tim.*
Pusat penyimpanan foto produk perusahaan. Unggah, kelola, dan ambil gambar produk dari satu tempat agar tim penjualan, gudang, dan marketing selalu memakai foto yang sama dan terbaru.

**8. Tomoro Activities** — *Tugas harian outlet, selesai tanpa ada yang terlewat.*
Manajemen tugas dan to-do list untuk operasional Tomoro Coffee. Bagikan tugas ke tim, pantau progresnya, dan pastikan rutinitas outlet berjalan konsisten setiap hari.

---

## 3. Arsitektur & Teknologi

| Aspek | Keputusan |
|---|---|
| Runtime | PHP rilis stabil terbaru, `declare(strict_types=1)` di semua file |
| Framework | **Tanpa framework** (PHP native + partial template) |
| Database | Tidak ada |
| Data | Array PHP di `config/apps.php` (dipetakan ke objek `readonly class`) |
| Template | PHP native, output di-escape dengan helper `e()` |
| CSS | Tailwind CSS (build statis via CLI, hasil `public/assets/css/app.css` di-commit) atau CSS murni jika ingin tanpa build step |
| JavaScript | Vanilla JS (± 1 file, < 3 KB) untuk filter & pencarian |
| Font | Self-hosted (hindari CDN agar tetap jalan di jaringan internal) |
| Hosting | LXC Proxmox (Ubuntu 24.04) di 192.168.31.58, Nginx + PHP-FPM; reverse proxy Caddy di 192.168.31.52 (lihat bagian 7) |
| Composer | Opsional; tidak ada dependensi runtime. Boleh dipakai hanya untuk autoload PSR-4 |

> **Catatan penting:** beberapa URL memakai domain `.local`, yang hanya bisa di-resolve dari jaringan internal. Landing page dihosting di LXC Proxmox dan diakses lewat Caddy dari jaringan yang sama (atau via VPN), dan **tidak boleh memuat aset dari CDN eksternal**.

---

## 4. Struktur Direktori

```
portal/
├── public/                     # document root web server
│   ├── index.php               # entry point
│   ├── favicon.svg
│   └── assets/
│       ├── css/app.css
│       ├── js/app.js           # filter & pencarian
│       ├── fonts/
│       └── img/
│           ├── logo.svg
│           └── screenshots/    # 1 file per aplikasi
│               ├── livo.webp
│               ├── ribbon-production.webp
│               ├── bulk-production.webp
│               ├── wms.webp
│               ├── inventory.webp
│               ├── minio.webp
│               ├── product-images.webp
│               ├── tomoro-activities.webp
│               └── placeholder.webp
├── config/
│   └── apps.php                # satu-satunya sumber data konten
├── src/
│   ├── App.php                 # readonly class model
│   ├── Category.php            # enum kategori
│   └── helpers.php             # e(), asset()
├── templates/
│   ├── layout.php
│   └── partials/
│       ├── header.php
│       ├── app-card.php
│       └── footer.php
├── tools/
│   └── screenshot.mjs          # script Playwright (opsional, lihat bagian 8)
├── composer.json               # opsional
└── README.md
```

Hanya folder `public/` yang boleh diekspos oleh web server.

---

## 5. Model Data

### 5.1 `src/Category.php`

```php
<?php
declare(strict_types=1);

enum Category: string
{
    case Warehouse  = 'Gudang & Logistik';
    case Production = 'Produksi';
    case Assets     = 'Aset & Inventaris';
    case Media      = 'Infrastruktur & Media';
    case Outlet     = 'Operasional Outlet';
}
```

### 5.2 `src/App.php`

```php
<?php
declare(strict_types=1);

final readonly class App
{
    public function __construct(
        public string $slug,
        public string $name,
        public string $tagline,
        public string $description,
        public string $url,
        public Category $category,
        public string $screenshot,   // nama file di assets/img/screenshots/
        public ?string $badge = null // contoh: 'Legacy', 'Baru'
    ) {}
}
```

### 5.3 `config/apps.php` (contoh 2 entri, sisanya mengikuti bagian 2)

```php
<?php
declare(strict_types=1);

return [
    new App(
        slug: 'livo',
        name: 'Livo WMS (Legacy)',
        tagline: 'Sistem gudang generasi pertama yang masih setia bekerja.',
        description: 'Aplikasi warehouse management system lama untuk mencatat barang masuk, keluar, dan posisi stok di gudang. Tetap tersedia untuk kebutuhan data historis dan proses yang belum bermigrasi ke WMS baru.',
        url: 'https://livo.wibindo.local/login',
        category: Category::Warehouse,
        screenshot: 'livo.webp',
        badge: 'Legacy',
    ),
    new App(
        slug: 'wms',
        name: 'WMS',
        tagline: 'Gudang lebih cerdas, stok lebih jujur.',
        description: 'Generasi terbaru warehouse management system perusahaan. Alur penerimaan, penyimpanan, dan pengeluaran barang dibuat lebih ringkas dengan data stok yang lebih akurat dan mudah ditelusuri.',
        url: 'https://wms.wibindo.local/auth/login',
        category: Category::Warehouse,
        screenshot: 'wms.webp',
        badge: 'Baru',
    ),
    // ... tambahkan 6 aplikasi lainnya sesuai bagian 2
];
```

Menambah/mengubah aplikasi di kemudian hari = edit satu file ini + taruh screenshot baru.

### 5.4 `src/helpers.php`

```php
<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function asset(string $path): string
{
    $file = __DIR__ . '/../public/assets/' . $path;
    $v = is_file($file) ? filemtime($file) : 0;   // cache busting
    return '/assets/' . $path . '?v=' . $v;
}
```

### 5.5 `public/index.php`

```php
<?php
declare(strict_types=1);

require __DIR__ . '/../src/Category.php';
require __DIR__ . '/../src/App.php';
require __DIR__ . '/../src/helpers.php';

/** @var App[] $apps */
$apps = require __DIR__ . '/../config/apps.php';

$pageTitle = 'Portal Web App Wibindo';
require __DIR__ . '/../templates/layout.php';
```

---

## 6. Desain Halaman

### 6.1 Struktur section (satu halaman)

1. **Header / Hero**
   - Logo perusahaan, judul: *"Semua aplikasi kerja Anda, satu tempat."*
   - Subjudul: *"Pilih aplikasi di bawah untuk mulai bekerja."*
   - Kolom pencarian (`<input type="search">`) dengan label aksesibel.
2. **Filter kategori**: deretan tombol/chip: `Semua`, `Gudang & Logistik`, `Produksi`, `Aset & Inventaris`, `Infrastruktur & Media`, `Operasional Outlet`.
3. **Grid kartu aplikasi**
4. **Footer**: nama perusahaan, kontak tim IT untuk bantuan akses, tahun.

### 6.2 Anatomi kartu aplikasi (`templates/partials/app-card.php`)

```
┌──────────────────────────────┐
│  [ screenshot, ideal 16:10 ]  │  <- lazy loading, rasio asli file
│  ┌──────┐                    │
│  │badge │ (opsional, pojok)  │
├──────────────────────────────┤
│  Kategori (teks kecil)       │
│  Nama Aplikasi (h3)          │
│  Tagline (tebal/italik)      │
│  Deskripsi (maks. 3-4 baris) │
│                              │
│  [ Buka Aplikasi  ↗ ]        │  <- link, target="_blank"
└──────────────────────────────┘
```

Contoh markup:

```php
<article class="app-card"
         data-category="<?= e($app->category->value) ?>"
         data-search="<?= e(mb_strtolower($app->name . ' ' . $app->tagline . ' ' . $app->description)) ?>">
  <img src="<?= asset('img/screenshots/' . $app->screenshot) ?>"
       alt="Tampilan aplikasi <?= e($app->name) ?>"
       width="1280" height="800" loading="lazy" decoding="async">
  <?php if ($app->badge): ?>
    <span class="badge"><?= e($app->badge) ?></span>
  <?php endif; ?>
  <div class="app-card__body">
    <p class="app-card__category"><?= e($app->category->value) ?></p>
    <h3><?= e($app->name) ?></h3>
    <p class="app-card__tagline"><?= e($app->tagline) ?></p>
    <p><?= e($app->description) ?></p>
    <a class="btn" href="<?= e($app->url) ?>" target="_blank" rel="noopener noreferrer">
      Buka Aplikasi <span aria-hidden="true">↗</span>
    </a>
  </div>
</article>
```

### 6.3 Grid & responsif

| Lebar layar | Kolom |
|---|---|
| < 640 px | 1 |
| 640-1023 px | 2 |
| ≥ 1024 px | 3 |
| ≥ 1536 px | 4 |

### 6.4 Gaya visual
- Gaya bersih dan modern: kartu putih/abu gelap dengan sudut membulat (`rounded-2xl`), bayangan halus, efek *hover* (naik 2-4 px + bayangan lebih tegas).
- Satu warna aksen utama (sesuaikan dengan warna brand perusahaan) untuk tombol dan chip aktif.
- Dark mode otomatis via `prefers-color-scheme` (pakai CSS variable).
- Hormati `prefers-reduced-motion` (matikan animasi hover/transisi).

### 6.5 Perilaku JavaScript (`public/assets/js/app.js`)
- Filter kategori: tampilkan/sembunyikan kartu berdasarkan `data-category`.
- Pencarian: cocokkan teks input ke `data-search` (debounce ± 150 ms).
- Jika tidak ada hasil, tampilkan pesan *"Aplikasi tidak ditemukan. Coba kata kunci lain."*
- Halaman **tetap berfungsi tanpa JS** (semua kartu tampil, filter hanya peningkatan).

---

## 7. Infrastruktur & Konfigurasi Server

### 7.1 Topologi

```
Karyawan (LAN/VPN)
      │  HTTPS 443
      ▼
┌──────────────────────────┐        HTTP 80         ┌───────────────────────────────┐
│ Caddy (reverse proxy)    │ ─────────────────────▶ │ LXC Proxmox: Ubuntu 24.04     │
│ 192.168.31.52            │                        │ 192.168.31.58                 │
│ - TLS (tls internal)     │                        │ Nginx + PHP-FPM               │
│ - allowlist jaringan     │                        │ /var/www/portal/public        │
└──────────────────────────┘                        └───────────────────────────────┘
```

| Komponen | Nilai |
|---|---|
| Hostname portal (usulan) | `portal.wibindo.local` → DNS/hosts mengarah ke **192.168.31.52** (Caddy), bukan ke .58 |
| Container | LXC Proxmox, template Ubuntu 24.04, IP **192.168.31.58** (statis) |
| Reverse proxy | Caddy di **192.168.31.52** (TLS terminasi di sini) |
| Web server di container | Nginx + PHP-FPM, hanya listen HTTP port 80 |
| Spesifikasi container (cukup) | 1 vCPU, 512 MB RAM, 4 GB disk, unprivileged container |

> Catatan: Caddy bisa juga dipakai langsung di dalam container (`php_fastcgi`) menggantikan Nginx. Blueprint ini memakai Nginx agar pemisahan peran jelas (Caddy = TLS/proxy, container = aplikasi). Keduanya dapat diterima.

### 7.2 Setup container (Ubuntu 24.04)

Repositori bawaan Ubuntu 24.04 hanya menyediakan PHP 8.3. Untuk PHP rilis stabil terbaru, gunakan PPA `ppa:ondrej/php`.

```bash
# 1. Update & paket dasar
apt update && apt upgrade -y
apt install -y software-properties-common ufw git

# 2. PHP terbaru (cek ketersediaan paket versi terbaru di PPA saat implementasi)
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y nginx php8.5-fpm php8.5-cli php8.5-mbstring

# 3. Direktori aplikasi
mkdir -p /var/www/portal
chown -R www-data:www-data /var/www/portal

# 4. Firewall: HTTP hanya dari Caddy, SSH hanya dari jaringan admin
ufw default deny incoming
ufw allow from 192.168.31.52 to any port 80 proto tcp
ufw allow from 192.168.31.0/24 to any port 22 proto tcp
ufw enable
```

Jika paket `php8.5-*` belum tersedia, pakai rilis PHP stabil terbaru yang ada di PPA (minimal 8.4) dan sesuaikan nama paket serta path socket di bawah.

### 7.3 Nginx di container (`/etc/nginx/sites-available/portal`)

```nginx
server {
    listen 80;
    server_name portal.wibindo.local;

    root /var/www/portal/public;
    index index.php;

    # Ambil IP asli klien dari Caddy (untuk log)
    set_real_ip_from 192.168.31.52;
    real_ip_header   X-Forwarded-For;

    # Header keamanan (HSTS diatur di Caddy)
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; frame-ancestors 'none'" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
    }

    # Cache aset statis
    location ~* \.(css|js|webp|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        # add_header di location menimpa header server-level, ulangi yang penting:
        add_header X-Content-Type-Options "nosniff" always;
    }

    # Blokir file tersembunyi
    location ~ /\.(?!well-known) { deny all; }

    server_tokens off;
}
```

```bash
ln -s /etc/nginx/sites-available/portal /etc/nginx/sites-enabled/portal
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

### 7.4 Caddy di 192.168.31.52 (tambahkan ke `Caddyfile`)

```caddyfile
portal.wibindo.local {
    tls internal

    encode zstd gzip

    header {
        Strict-Transport-Security "max-age=31536000"
        -Server
    }

    # Hanya jaringan internal (sesuaikan dengan subnet LAN/VPN yang dipakai)
    @internal remote_ip 192.168.31.0/24
    handle @internal {
        reverse_proxy 192.168.31.58:80
    }
    handle {
        respond "Akses ditolak" 403
    }

    log {
        output file /var/log/caddy/portal.access.log
    }
}
```

```bash
caddy validate --config /etc/caddy/Caddyfile
systemctl reload caddy
```

Catatan:
- `tls internal` memakai CA internal Caddy. Pastikan **root CA Caddy** sudah terpasang di perangkat karyawan (kemungkinan sudah, jika aplikasi `.wibindo.local` lain memakai cara yang sama). Jika perusahaan memakai sertifikat dari CA lain, ganti dengan direktif `tls /path/cert.pem /path/key.pem`.
- Jika karyawan mengakses dari VPN atau subnet lain, tambahkan subnet tersebut pada matcher `remote_ip`.
- Caddy otomatis mengirim `X-Forwarded-For`, `X-Forwarded-Proto`, dan `X-Forwarded-Host` ke Nginx.
- Pastikan DNS internal (atau file hosts) memetakan `portal.wibindo.local` ke **192.168.31.52**.

### 7.5 PHP (`/etc/php/8.5/fpm/php.ini`)
- `expose_php = Off`, `display_errors = Off`, `log_errors = On`.
- OPcache aktif (sejak PHP 8.5 sudah menjadi bagian inti; cukup pastikan `opcache.enable=1`).
- `systemctl restart php8.5-fpm` setelah perubahan.

### 7.6 Deployment

Cara paling sederhana (tanpa CI):

```bash
# Kondisi repository saat ini: branch master, tanpa remote Git.
# git pull belum dapat digunakan sebelum remote dan branch deployment resmi dikonfigurasi.
# Ikuti prosedur penyalinan aman di DEPLOYMENT.md.
# Hanya salin public/, config/, src/, dan templates/.
# Jangan salin legacy-nextjs/node_modules atau legacy-nextjs/.next.
```

Tidak ada langkah build di server jika `app.css` hasil build sudah di-commit. Disarankan membuat **snapshot/backup LXC** di Proxmox sebelum perubahan besar. Jangan gunakan PHP built-in server (`php -S`) sebagai server produksi; produksi memakai Nginx + PHP-FPM dengan document root `public/`.

### 7.7 Verifikasi

| Pengecekan | Perintah / hasil yang diharapkan |
|---|---|
| Nginx langsung dari Caddy | `curl -I http://192.168.31.58` dari 192.168.31.52 → `200 OK` |
| Port 80 tertutup dari mesin lain | `curl http://192.168.31.58` dari PC biasa → timeout/ditolak |
| Lewat Caddy (HTTPS) | `curl -I https://portal.wibindo.local` → `200 OK`, ada header HSTS |
| Akses dari luar subnet | → `403 Akses ditolak` |
| Header keamanan | Cek `X-Frame-Options`, `Content-Security-Policy` di respons |

---

## 8. Panduan Screenshot

| Aspek | Ketentuan |
|---|---|
| Ukuran | 1280 × 800 px (rasio 16:10) |
| Format | **WebP** (kualitas 80), target < 150 KB per file |
| Nama file | `<slug>.webp` sesuai tabel bagian 2 |
| Konten | Halaman yang paling representatif (dashboard/halaman utama setelah login), bukan sekadar halaman login, jika memungkinkan |
| Data | **Gunakan data dummy atau data yang sudah disamarkan.** Jangan menampilkan data pelanggan, harga rahasia, token, atau kredensial |
| Tema | Gunakan mode terang untuk konsistensi |
| Fallback | `placeholder.webp` jika screenshot belum tersedia |

Cara pengambilan:
1. **Manual**: browser dengan viewport 1280×800, ambil screenshot, lalu konversi ke WebP (`cwebp -q 80 in.png -o out.webp`).
2. **Otomatis (opsional)**: script Playwright di `tools/screenshot.mjs` yang membuka tiap URL (dengan sesi login akun demo), mengatur viewport 1280×800, lalu menyimpan ke `public/assets/img/screenshots/`. Jalankan hanya di mesin developer, bukan di server produksi.

---

## 9. Keamanan & Akses

- Seluruh link mengarah ke aplikasi internal, jadi daftar ini **tidak boleh dipublikasikan ke internet umum**. Batasi akses portal ke jaringan internal/VPN: allowlist `remote_ip` di Caddy, dan `ufw` di container hanya mengizinkan port 80 dari 192.168.31.52.
- Semua link eksternal memakai `rel="noopener noreferrer"`.
- Seluruh output template di-escape dengan `e()`.
- Tidak ada input pengguna yang diproses di sisi server (halaman murni baca-saja).
- Pastikan sertifikat HTTPS valid/terpercaya di perangkat karyawan, termasuk untuk domain `.local` (root CA Caddy internal harus terpasang di perangkat).
- Layanan sensitif (MinIO) diberi badge `Untuk tim IT` sebagai petunjuk, bukan sebagai kontrol akses; kontrol akses tetap di aplikasinya masing-masing.

---

## 10. SEO, Aksesibilitas & Performa

- `<html lang="id">`, `<title>`, `<meta name="description">`, dan favicon.
- `<meta name="robots" content="noindex, nofollow">` karena portal internal.
- Kontras warna minimal WCAG AA; fokus keyboard terlihat jelas; semua gambar punya `alt` bermakna; tombol/link dapat diakses keyboard.
- Gambar dengan atribut `width`/`height` (mencegah layout shift), `loading="lazy"`, dan `decoding="async"`.
- Target Lighthouse: Performance ≥ 95, Accessibility ≥ 95, Best Practices ≥ 95.
- Total bobot halaman awal < 1 MB.

---

## 11. Rencana Pengerjaan

| Tahap | Pekerjaan | Estimasi |
|---|---|---|
| 1 | Setup proyek, struktur folder, helper, model data | 5 hari |
| 2 | Layout, header, footer, kartu aplikasi (HTML + CSS) | 5 hari |
| 3 | Filter kategori & pencarian (JS) | 5 hari |
| 4 | Pengambilan & optimasi 8 screenshot | 5 hari |
| 5 | Setup LXC (Nginx, PHP), Caddy, header keamanan, pembatasan akses | 5 hari |
| 6 | QA (responsif, dark mode, aksesibilitas, Lighthouse) & serah terima | 5 hari |
| | **Total** | **± 30 hari** |

---

## 12. Kriteria Penerimaan (Definition of Done)

- [ ] Kedelapan aplikasi tampil dengan screenshot, nama, tagline, deskripsi, dan tombol yang benar.
- [ ] Setiap tombol membuka URL yang tepat di tab baru.
- [ ] Filter kategori dan pencarian berfungsi; halaman tetap terbaca tanpa JavaScript.
- [ ] Tampilan baik di mobile (360 px), tablet, dan desktop; dark mode berfungsi.
- [ ] Tidak ada request ke domain eksternal (cek tab Network).
- [ ] Menambah aplikasi baru cukup dengan mengedit `config/apps.php` dan menambah file screenshot.
- [ ] Skor Lighthouse sesuai target pada bagian 10.
- [ ] Akses portal dibatasi ke jaringan internal (Caddy `remote_ip` + `ufw` di container).
- [ ] Container 192.168.31.58 hanya menerima HTTP dari 192.168.31.52; semua pengecekan di bagian 7.7 lulus.
- [ ] `README.md` berisi cara menjalankan lokal (`php -S localhost:8000 -t public`), cara menambah aplikasi, dan cara deploy.

---

## 13. Pengembangan Lanjutan (Opsional)

- **Indikator status** (online/offline) per aplikasi lewat skrip cron PHP CLI yang menulis `status.json`, lalu dibaca JS di sisi klien. Tetap tanpa database.
- **Build statis**: skrip `build.php` yang me-render `index.php` menjadi `dist/index.html` untuk di-host di web server apa pun.
- **Favorit pengguna** disimpan di `localStorage` browser.
- **Multi-bahasa** (ID/EN) dengan file terjemahan array PHP.
- Integrasi SSO jika nanti seluruh aplikasi memakai penyedia identitas yang sama.

---

## 14. Pertanyaan Terbuka untuk Tim

1. Warna brand dan logo resmi apa yang dipakai?
2. Domain/hostname portal ini (usulan: `portal.wibindo.local`, dipetakan ke 192.168.31.52)? Subnet LAN/VPN mana saja yang diizinkan di Caddy?
3. Apakah ada akun demo untuk pengambilan screenshot di tiap aplikasi?
4. Siapa kontak IT yang ditampilkan di footer?
5. Apakah Livo (legacy) akan dipensiunkan pada tanggal tertentu, sehingga perlu catatan "akan dinonaktifkan"?
