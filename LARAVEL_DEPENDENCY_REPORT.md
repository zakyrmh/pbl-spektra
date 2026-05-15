# Laporan Dependency Laravel

Dokumentasi ini menjelaskan dependency package Laravel yang ditemukan pada proyek `pbl-spektra` menggunakan pendekatan 5W+1H.

## Sumber Referensi

- File konfigurasi: `composer.json`
- Package registry: Packagist

## Dependency Laravel

### 1. laravel/framework
- Who: Dibuat dan dipelihara oleh tim Laravel.
- What: Kerangka kerja inti Laravel untuk membangun aplikasi PHP modern.
- When: Versi yang dibutuhkan adalah `^13.0`.
- Where: Terdaftar dalam bagian `require` pada `composer.json`.
- Why: Menyediakan fungsi utama Laravel seperti routing, Eloquent ORM, middleware, dan sistem templating.
- How: Diinstal melalui Composer saat menjalankan `composer install`.
- Referensi: https://packagist.org/packages/laravel/framework

### 2. laravel/tinker
- Who: Dibuat oleh tim Laravel.
- What: Paket CLI untuk menjalankan REPL interaktif dengan lingkungan aplikasi Laravel.
- When: Versi yang dibutuhkan adalah `^3.0`.
- Where: Terdaftar dalam bagian `require` pada `composer.json`.
- Why: Memudahkan pengembang untuk menguji kode, memanggil model, dan menjalankan perintah PHP secara langsung.
- How: Diinstal melalui Composer agar perintah `php artisan tinker` tersedia.
- Referensi: https://packagist.org/packages/laravel/tinker

### 3. laravel/boost
- Who: Dibuat oleh tim Laravel untuk tooling pengembangan.
- What: Paket dukungan untuk Laravel Boost, menambah kemampuan pengembangan aplikasi dengan generator dan utilitas.
- When: Versi yang dibutuhkan adalah `^2.2`.
- Where: Terdaftar dalam bagian `require-dev` pada `composer.json`.
- Why: Digunakan selama pengembangan dan build untuk fitur Boost, tanpa diperlukan di produksi.
- How: Diinstal melalui Composer ketika dependency development diunduh.
- Referensi: https://packagist.org/packages/laravel/boost

### 4. laravel/pail
- Who: Dibuat oleh tim Laravel.
- What: Paket utilitas CLI untuk memfasilitasi alur kerja pengembangan Laravel.
- When: Versi yang dibutuhkan adalah `^1.2.5`.
- Where: Terdaftar dalam bagian `require-dev` pada `composer.json`.
- Why: Menyediakan alat bantu pengembangan yang tidak dibutuhkan di lingkungan produksi.
- How: Diinstal sebagai dependency development.
- Referensi: https://packagist.org/packages/laravel/pail

### 5. laravel/pint
- Who: Dibuat oleh tim Laravel.
- What: Alat pemformat kode PHP yang mengikuti standar Laravel.
- When: Versi yang dibutuhkan ditentukan sebagai `*`.
- Where: Terdaftar dalam bagian `require-dev` pada `composer.json`.
- Why: Untuk memastikan konsistensi gaya kode dan format otomatis selama pengembangan.
- How: Diinstal sebagai dependency development dan biasanya dijalankan menggunakan `vendor/bin/pint`.
- Referensi: https://packagist.org/packages/laravel/pint

## Ringkasan

Dependency Laravel pada proyek ini terdiri dari paket inti dan paket development:

- `require`: `laravel/framework`, `laravel/tinker`
- `require-dev`: `laravel/boost`, `laravel/pail`, `laravel/pint`

Dokumentasi ini dibuat berdasarkan konten `composer.json` dan daftar package Laravel di Packagist.
