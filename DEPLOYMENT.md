# Runbook Deployment Internal — Portal Web App Wibindo

Status dokumen: persiapan deployment. Tidak ada perubahan server, DNS, firewall, Caddy, atau Nginx yang dilakukan dari repository ini.

## Target dan topologi

| Komponen | Nilai |
|---|---|
| Hostname | `portal.wibindo.local` |
| Caddy / TLS termination | `192.168.31.52` |
| LXC Ubuntu | `192.168.31.58` |
| Nginx upstream | HTTP port `80`, hanya dari Caddy |
| Document root | `/var/www/portal/public` |
| Runtime | PHP `8.5.12` + PHP-FPM |

Alur koneksi yang diharapkan:

```text
Client LAN/VPN -- HTTPS 443 --> Caddy 192.168.31.52
                                  -- HTTP 80 --> Nginx 192.168.31.58
                                                    -- Unix socket --> PHP-FPM 8.5
```

Hostname harus mengarah ke `192.168.31.52`, bukan langsung ke `192.168.31.58`.

## Persyaratan runtime

- Ubuntu 24.04 LXC, tanpa database dan tanpa Composer/Node.js runtime.
- PHP 8.5.12, `php8.5-fpm`, `php8.5-cli`, `php8.5-mbstring`, dan OPcache (`php8.5-opcache` bila dipisahkan oleh paket distro).
- Ekstensi yang dipakai aplikasi: `mbstring` untuk pencarian case-insensitive Unicode. `ctype`, `filter`, `json`, `pcre`, `SPL`, dan `standard` adalah bagian dari instalasi PHP umum.
- OPcache aktif untuk FPM. `display_errors=Off`, `log_errors=On`, dan `expose_php=Off`.
- Nginx dan Caddy sesuai topologi di atas.

Tidak ada koneksi database, autentikasi portal, CDN, font eksternal, atau build step di server. `legacy-nextjs/` tidak boleh dihapus dari repository kerja; folder itu tidak diperlukan sebagai document root atau runtime portal PHP.

Pemeriksaan lokal terhadap target runtime:

```text
PHP 8.5.12 — tersedia di C:\tools\php85\php.exe
mbstring    — tersedia
OPcache     — tersedia dan aktif sebagai Zend extension
```

## Struktur dan permission

Hanya `public/` yang boleh menjadi document root. Source PHP tetap berada di luar web root:

```text
/var/www/portal/
├── public/                 # document root Nginx
│   ├── index.php
│   └── assets/
├── config/apps.php         # tidak dapat diakses langsung melalui HTTP
├── src/                    # tidak dapat diakses langsung melalui HTTP
└── templates/              # tidak dapat diakses langsung melalui HTTP
```

Rekomendasi permission untuk source read-only:

```bash
chown -R root:www-data /var/www/portal
find /var/www/portal -type d -exec chmod 0750 {} \;
find /var/www/portal -type f -exec chmod 0640 {} \;
```

Jika proses deploy memakai user khusus, berikan akses baca melalui group `www-data` atau group deploy yang sesuai; jangan memberikan write kepada worker Nginx/PHP-FPM. Pastikan hanya `public/assets/` yang perlu dilayani sebagai file statis. Simpan backup/snapshot sebelum mengganti release.

## Konfigurasi PHP-FPM

Di `/etc/php/8.5/fpm/php.ini`, pastikan minimal:

```ini
expose_php = Off
display_errors = Off
log_errors = On
opcache.enable = 1
```

Validasi dan restart setelah perubahan:

```bash
php8.5 -v
php8.5 -m | grep -E 'mbstring|Zend OPcache'
php-fpm8.5 -t
systemctl restart php8.5-fpm
```

## Konfigurasi Nginx di LXC

Simpan sebagai `/etc/nginx/sites-available/portal` dan sesuaikan socket bila nama service berbeda:

```nginx
server {
    listen 80;
    server_name portal.wibindo.local;

    root /var/www/portal/public;
    index index.php;
    autoindex off;

    # Hanya Caddy yang boleh menjadi upstream HTTP.
    set_real_ip_from 192.168.31.52;
    real_ip_header X-Forwarded-For;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), payment=()" always;
    add_header Content-Security-Policy "default-src 'self'; base-uri 'none'; object-src 'none'; form-action 'none'; img-src 'self' data:; style-src 'self'; script-src 'self'; frame-ancestors 'none'" always;

    # Defense-in-depth bila source tanpa sengaja diletakkan di bawah root.
    location ~ ^/(config|src|templates)(/|$) {
        return 404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
    }

    location ~* \.(css|js|webp|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        add_header X-Content-Type-Options "nosniff" always;
    }

    location ~ /\. {
        deny all;
    }

    server_tokens off;
}
```

Aktifkan dengan `nginx -t` terlebih dahulu, kemudian reload. Hapus/disable site default hanya setelah partner memastikan tidak ada layanan lain yang bergantung padanya.

```bash
ln -s /etc/nginx/sites-available/portal /etc/nginx/sites-enabled/portal
nginx -t
systemctl reload nginx
```

Firewall LXC harus membatasi TCP/80 hanya dari `192.168.31.52`; SSH dibatasi ke subnet administrasi. Jangan membuka port 80 LXC ke LAN umum.

## Konfigurasi Caddy di `192.168.31.52`

Tambahkan blok berikut ke Caddyfile. `192.168.31.0/24` adalah allowlist LAN berdasarkan target saat ini; partner harus menambahkan subnet VPN yang resmi bila diperlukan.

```caddyfile
portal.wibindo.local {
    tls internal
    encode zstd gzip

    header {
        Strict-Transport-Security "max-age=31536000"
        -Server
    }

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

Validasi dan reload:

```bash
caddy validate --config /etc/caddy/Caddyfile
systemctl reload caddy
```

`tls internal` memakai CA internal Caddy. Root CA tersebut harus terpasang dan dipercaya di perangkat karyawan; jika organisasi memakai CA internal lain, partner mengganti konfigurasi TLS sesuai sertifikat resmi. HSTS hanya diaktifkan setelah HTTPS internal sudah tervalidasi.

## Kondisi repository dan metode deploy

Pemeriksaan repository saat dokumen ini disesuaikan (2 November 2026):

- Branch aktif: `master`.
- Remote Git: belum ada; `git remote -v` tidak menghasilkan output.
- Dengan kondisi tersebut, `git pull origin main` tidak dapat digunakan. Jangan mengasumsikan remote `origin` atau branch `main` sebelum keduanya dikonfigurasi resmi.

Deployment saat ini harus dilakukan dengan menyalin artefak dari mesin sumber ke server, bukan dengan `git pull`. Metode resmi (arsip, `rsync`, atau mekanisme lain dari partner DevOps) dan lokasi checkout sumber masih perlu dikonfirmasi.

### Penyalinan artefak yang aman

Yang disalin hanya file runtime:

- `public/`
- `config/`
- `src/`
- `templates/`

`public/` tetap menjadi document root. `config/`, `src/`, dan `templates/` harus berada di luar `public/` sebagai sibling.

Jangan menyalin:

- `legacy-nextjs/`, termasuk `legacy-nextjs/node_modules/` dan `legacy-nextjs/.next/`
- `.git/`
- file konfigurasi rahasia, kredensial, atau `.env` bila ada
- cache dan artefak build lokal

Contoh membuat arsip dari mesin sumber (hanya path runtime):

```bash
tar -C <lokasi-checkout-lokal> \
  -czf portal-release.tar.gz \
  public config src templates
```

Setelah snapshot/backup dibuat, arsip dapat diekstrak ke direktori release di server. Contoh tujuan yang sudah tercatat di runbook ini adalah `/var/www/portal`; konfirmasi path final dengan partner DevOps:

```bash
tar -C /var/www/portal -xzf portal-release.tar.gz
```

Alternatif `rsync` tanpa menghapus file di server:

```bash
rsync -a <lokasi-checkout-lokal>/public/ /var/www/portal/public/
rsync -a <lokasi-checkout-lokal>/config/ /var/www/portal/config/
rsync -a <lokasi-checkout-lokal>/src/ /var/www/portal/src/
rsync -a <lokasi-checkout-lokal>/templates/ /var/www/portal/templates/
```

Jangan menggunakan PHP built-in server (`php -S`) sebagai server produksi; perintah tersebut hanya untuk pengembangan lokal. Nginx + PHP-FPM tetap menjadi runtime produksi.

Partner DevOps bertanggung jawab atas konfigurasi Nginx, PHP-FPM, Caddy, TLS, firewall, backup, dan rollback di server. Repository hanya menyediakan source aplikasi dan contoh konfigurasi.

### Jika Git akan digunakan di masa depan

`git pull` baru dapat digunakan setelah:

1. Remote Git resmi (nama dan URL) dikonfigurasi oleh pemilik repository atau partner DevOps.
2. Branch deployment disepakati. Saat ini satu-satunya branch adalah `master`; jangan mengasumsikan branch `main`.
3. Akses kredensial deployment dan mekanisme otorisasi disetujui.

Tidak ada remote yang ditambahkan dan tidak ada perubahan konfigurasi Git dalam dokumen ini.

## Prosedur deploy dan rollback

1. Buat snapshot LXC Proxmox atau backup arsip `/var/www/portal` sebelum perubahan.
2. Salin hanya `public/`, `config/`, `src/`, dan `templates/` sesuai prosedur penyalinan di atas. Jangan salin `legacy-nextjs/`, `legacy-nextjs/node_modules/`, `legacy-nextjs/.next/`, `.git/`, atau file rahasia.
3. Jalankan lint PHP 8.5.12 dan pemeriksaan permission.
4. Jalankan `nginx -t`, reload Nginx, lalu verifikasi upstream dari host Caddy.
5. Validasi Caddy, reload Caddy, dan lakukan smoke test dari jaringan yang diizinkan.
6. Jika gagal, hentikan promosi release, pulihkan snapshot/arsip ke `/var/www/portal`, jalankan `nginx -t`, restart/reload service yang relevan, lalu ulangi smoke test.

Contoh backup arsip sebelum deploy:

```bash
tar -C /var/www -czf /var/backups/portal-$(date +%Y%m%d-%H%M%S).tar.gz portal
```

Rollback harus memakai backup/snapshot yang sudah diidentifikasi, bukan menghapus source secara membabi-buta. Simpan log perubahan dan hash release yang dipromosikan.

## Checklist verifikasi setelah deployment

Jalankan dari lokasi yang sesuai dan catat hasilnya:

```bash
# Di LXC
php8.5 -v
php8.5 -m | grep -E 'mbstring|Zend OPcache'
find /var/www/portal -name '*.php' -not -path '/var/www/portal/legacy-nextjs/*' -print0 | xargs -0 -n1 php8.5 -l
nginx -t

# Dari host Caddy
curl -i -H 'Host: portal.wibindo.local' http://192.168.31.58/
curl -I -H 'Host: portal.wibindo.local' http://192.168.31.58/assets/css/app.css

# Dari client internal dengan CA Caddy terpasang
curl -I https://portal.wibindo.local/
curl -s https://portal.wibindo.local/ | grep -c 'class="app-card"'   # harapannya 8
```

Verifikasi manual:

- Status HTTPS `200`, HSTS, dan header keamanan hadir.
- CSS, JavaScript, logo, favicon, dan placeholder screenshot merespons tanpa 404.
- Search, filter kategori, reset search, tautan delapan aplikasi, dark mode, keyboard focus, dan layout 360 px / tablet / desktop diperiksa.
- `/config/apps.php`, `/src/helpers.php`, dan `/templates/layout.php` tidak mengembalikan source PHP; target yang benar adalah 404 atau fallback yang tidak membocorkan source.
- Akses langsung ke `192.168.31.58:80` dari mesin selain Caddy ditolak oleh firewall.
- Akses dari subnet di luar allowlist Caddy mendapat `403 Akses ditolak`.

## Aset dan informasi yang masih diperlukan dari tim Wibindo

- Logo resmi dan favicon resmi; file saat ini adalah mark placeholder bergaya `W`.
- Delapan screenshot aplikasi asli yang sudah disamarkan, masing-masing sesuai slug dan rasio 1280×800. Saat ini semua kartu fallback ke `placeholder.webp`.
- Alamat email tim IT pengganti `it@wibindo.local`.
- Daftar subnet LAN/VPN yang boleh mengakses portal dan root CA yang harus dipercaya client.
- Konfirmasi kepemilikan/penamaan domain `wibindo.local`, URL final delapan aplikasi, dan apakah Livo legacy punya tanggal pensiun.
- Konfirmasi metode deployment resmi (arsip, `rsync`, atau Git), lokasi checkout sumber, path release di server, serta URL dan branch remote Git bila kelak digunakan.
- Konfirmasi bahwa `/var/www/portal` tetap menjadi path final dan bahwa partner DevOps bertanggung jawab atas Nginx, PHP-FPM, Caddy, TLS, firewall, backup, dan rollback.

Tidak ada identitas resmi perusahaan yang dibuat atau diasumsikan oleh portal ini.
