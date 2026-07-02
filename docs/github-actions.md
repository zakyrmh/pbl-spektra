# Dokumentasi GitHub Actions CI/CD - SPEKTRA
Sistem Manajemen Antrean Digital MPP Kota Sawahlunto

---

## 1. Workflow yang Digunakan
Workflow yang digunakan adalah **Laravel Integration Workflow**. 

Workflow ini dirancang untuk melakukan pengujian otomatis terhadap kode aplikasi Laravel 11/12 yang menggunakan PHP 8.4 dan Node.js 20. Workflow ini memastikan bahwa setiap perubahan kode tidak merusak fitur-fitur yang sudah ada (*regression prevention*) dengan cara membangun lingkungan virtual, memasang seluruh dependensi, menjalankan migrasi basis data SQLite, dan mengeksekusi test suite (menggunakan Pest/PHPUnit) secara otomatis.

---

## 2. Lokasi File
File konfigurasi workflow GitHub Actions ini terletak pada direktori repositori berikut:
* **Path file:** [.github/workflows/laravel.yml](file:///C:/laragon/www/pbl-spektra/.github/workflows/laravel.yml)

---

## 3. Trigger
Alur kerja (pipeline) ini dikonfigurasi untuk terpicu (*triggered*) secara otomatis oleh event berikut:

1. **Push:** Setiap kali ada pengiriman commit baru (*push*) ke cabang/branch:
   - `main`
   - `develop`
2. **Pull Request:** Setiap kali ada pengajuan penggabungan kode (*pull request*) yang menargetkan cabang/branch:
   - `main`
   - `develop`

```yaml
on:
  push:
    branches: [ "main", "develop" ]
  pull_request:
    branches: [ "main", "develop" ]
```

---

## 4. Tahapan Workflow
Workflow berjalan di dalam satu job utama bernama `laravel-tests` di lingkungan mesin virtual Ubuntu terbaru (`ubuntu-latest`). Tahapan langkahnya (*steps*) didefinisikan sebagai berikut:

1. **Checkout Code:**
   - Mengunduh kode sumber terbaru dari repositori ke dalam runner virtual.
   - Menggunakan: `actions/checkout@v4`
2. **Setup PHP:**
   - Mempersiapkan PHP versi `8.4` beserta ekstensi yang diperlukan (seperti `pdo_sqlite`, `bcmath`, `mbstring`, dll.).
   - Menggunakan: `shivammathur/setup-php@v2`
3. **Copy .env File:**
   - Membuat file `.env` untuk kebutuhan lingkungan pengujian berdasarkan template `.env.example`.
4. **Cache Composer Dependencies:**
   - Melakukan caching folder `vendor` berdasarkan hash file `composer.lock` guna mempercepat proses build pada eksekusi berikutnya.
   - Menggunakan: `actions/cache@v4`
5. **Install Composer Dependencies:**
   - Menginstal paket dependensi PHP secara non-interaktif tanpa progres untuk efisiensi waktu eksekusi.
6. **Setup Node.js:**
   - Mempersiapkan Node.js versi `20` beserta manajemen cache NPM.
   - Menggunakan: `actions/setup-node@v4`
7. **Install NPM Dependencies:**
   - Memasang dependensi JavaScript (NPM) menggunakan perintah `npm ci` (Husky dimatikan dengan `HUSKY: 0` agar tidak memicu git hooks saat pengujian).
8. **Generate Key:**
   - Membuat kunci enkripsi aplikasi via `php artisan key:generate`.
9. **Directory Permissions:**
   - Mengatur izin akses direktori (`chmod -R 775`) pada folder `storage` dan `bootstrap/cache` agar dapat ditulis oleh server penguji.
10. **Create Database:**
    - Membuat folder basis data dan berkas database kosong `database/database.sqlite`.
11. **Run Migrations:**
    - Menjalankan seluruh migrasi database ke SQLite via `php artisan migrate --force`.
12. **Execute Tests via Pest:**
    - Mengeksekusi seluruh skenario pengujian unit dan fitur menggunakan framework Pest/PHPUnit via `php artisan test`.

---

## 5. Hasil Workflow

### Status Badge
Status build dari pipeline integrasi ini dapat dipantau langsung melalui status badge dinamis di bawah ini:

[![Laravel CI/CD Status](https://github.com/zakyrmh/pbl-spektra/actions/workflows/laravel.yml/badge.svg)](https://github.com/zakyrmh/pbl-spektra/actions)

*Lencana di atas akan secara otomatis menunjukkan status **passing** (warna hijau) jika seluruh tahapan di atas sukses, atau **failing** (warna merah) jika terdapat pengujian yang gagal.*

### Screenshot Hasil Eksekusi
Berikut adalah dokumentasi visual hasil eksekusi pengujian otomatis pada GitHub Actions yang berhasil melewati seluruh tahapan (*all checks passed*):

![Hasil Sukses GitHub Actions Run](file:///C:/laragon/www/pbl-spektra/docs/screenshots/github-actions-run.png)

*(Catatan: Pastikan untuk menaruh berkas gambar hasil screenshot terbaru di path `docs/screenshots/github-actions-run.png` apabila melakukan pembaruan dokumentasi visual).*
