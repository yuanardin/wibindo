# Screenshot Aplikasi

Target file: `public/assets/img/screenshots/<slug>.webp`.

Ketentuan:

- Viewport 1280 x 800 px, rasio 16:10.
- Format WebP kualitas 80, target di bawah 150 KB.
- Gunakan mode terang dan data dummy atau data yang disamarkan.
- Ambil halaman paling representatif setelah login, bukan halaman login, jika memungkinkan.
- Jangan menampilkan kredensial, token, data pelanggan, atau informasi rahasia.

## Manual

1. Buka aplikasi pada browser dengan viewport 1280 x 800.
2. Ambil screenshot halaman utama.
3. Konversi ke WebP:

```bash
cwebp -q 80 screenshot.png -o public/assets/img/screenshots/<slug>.webp
```

4. Periksa ukuran file di bawah 150 KB.

## Otomatis (Opsional)

Script Playwright tidak disertakan sebagai dependency runtime. Jika ingin otomasi, pasang Playwright di mesin developer, siapkan sesi login akun demo, lalu ambil screenshot untuk setiap URL di `config/apps.php`. Jalankan hanya di mesin developer, bukan di server produksi.

Jika file screenshot belum tersedia atau tidak valid, helper `screenshot()` otomatis memakai `placeholder.webp`.
