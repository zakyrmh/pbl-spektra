# Dokumentasi CI/CD Pipeline (GitHub Actions)

Dokumen ini mendokumentasikan spesifikasi alur kerja Integrasi Berkelanjutan (Continuous Integration / CI) yang dirancang untuk proyek **PBL Spektra — Sistem Manajemen Antrean MPP Kota Sawahlunto**. Pipeline ini memastikan kualitas kode tetap terjaga secara otomatis pada setiap kontribusi sebelum digabungkan ke cabang utama.

---

## 🎯 1. Tujuan Workflow (Workflow Purpose)

Pipeline CI dirancang untuk mengotomatiskan proses verifikasi kualitas kode saat terjadi perubahan pada repositori. Tujuan utama dari pipeline ini meliputi:
1. **Verifikasi Sintaksis Kode**: Memastikan tidak ada kesalahan sintaksis PHP yang dapat menyebabkan aplikasi mengalami kegagalan *runtime*.
2. **Uji Validitas Dependensi**: Memastikan seluruh pustaka pihak ketiga (*packages*) yang terdaftar di `composer.json` dapat terinstal dengan lancar tanpa konflik versi.
3. **Pengujian Otomatis (Automated Testing)**: Menjalankan skenario pengujian unit dan fitur (Unit & Feature Tests) menggunakan PHPUnit/Pest untuk menjamin fungsionalitas bisnis (seperti pemesanan antrean, verifikasi check-in, pemanggilan antrean) tetap berfungsi dengan benar.

---

## 📂 2. Lokasi Berkas Konfigurasi

Alur kerja GitHub Actions ini didefinisikan dalam berkas berformat YAML yang diletakkan pada repositori proyek di path berikut:
```text
.github/workflows/ci.yml
```

---

## ⚡ 3. Pemicu Pipeline (Trigger Definition)

Pipeline ini dikonfigurasi untuk aktif secara otomatis ketika terjadi aktivitas perubahan kode pada cabang-cabang utama pengembangan, yaitu cabang `main` dan cabang `fitur-dependency`.

### Pemicu Event:
- **`push`**: Setiap kali developer melakukan pengiriman commit baru ke cabang `main` atau `fitur-dependency`.
- **`pull_request`**: Setiap kali diajukan permohonan penggabungan kode (*Pull Request*) yang menargetkan cabang `main` atau `fitur-dependency`.

### Contoh Blok Konfigurasi YAML:
```yaml
name: Continuous Integration - SPEKTRA

on:
  push:
    branches:
      - main
      - fitur-dependency
  pull_request:
    branches:
      - main
      - fitur-dependency
```

---

## ⛓️ 4. Tahapan Pipeline (Pipeline Stages Breakdown)

Alur kerja pipeline dibagi menjadi beberapa tahapan berurutan di dalam sebuah *job* bernama `laravel-tests` yang berjalan pada sistem operasi virtual ubuntu terbaru (`ubuntu-latest`):

### Langkah 1: Checkout Codebase
Mengunduh salinan kode sumber proyek dari repositori GitHub ke runner/container virtual GitHub Actions.
- **Action**: `actions/checkout@v4`

### Langkah 2: Setup PHP Environment
Menyiapkan lingkungan PHP dengan versi spesifik yang digunakan oleh aplikasi, serta mengaktifkan ekstensi PHP yang diperlukan.
- **Action**: `shivamurthapa/setup-php@v2`
- **Versi PHP**: `8.2`
- **Ekstensi PHP Wajib**: `mbstring`, `xml`, `bcmath`, `gd`, `zip`, `mysql`, `sqlite3`
- **Driver PHP**: `sqlite` (digunakan untuk mempercepat proses database testing di memori virtual)

### Langkah 3: Install Dependensi Composer
Memasang paket dependensi PHP secara cepat dan efisien dengan mengabaikan pesan progres interaktif untuk menghemat waktu build.
- **Perintah**: 
  ```bash
  composer install --no-progress --prefer-dist --no-interaction
  ```

### Langkah 4: Menjalankan Uji Coba Otomatis (Testing)
Menyiapkan konfigurasi pengujian lingkungan lokal, membuat database pengujian berbasis SQLite di memori, dan menjalankan test suite aplikasi.
- **Perintah Setup**:
  ```bash
  cp .env.example .env.testing
  php artisan key:generate --env=testing
  ```
- **Perintah Eksekusi Test**:
  ```bash
  php artisan test
  ```
  *(Menjalankan seluruh pengujian unit dan fitur menggunakan PHPUnit/Pest)*

---

## 📝 5. Lampiran Konfigurasi Penuh (`ci.yml`)

Berikut adalah berkas konfigurasi lengkap yang siap dipasang di dalam folder `.github/workflows/ci.yml`:

```yaml
name: Continuous Integration

on:
  push:
    branches:
      - main
      - fitur-dependency
  pull_request:
    branches:
      - main
      - fitur-dependency

jobs:
  laravel-tests:
    runs-on: ubuntu-latest

    services:
      # Opsional: Jika pengujian membutuhkan database MySQL fisik di GitHub Runner.
      # Namun, demi kecepatan, disarankan menggunakan database SQLite (:memory:) saat testing.
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ALLOW_EMPTY_PASSWORD: yes
          MYSQL_DATABASE: pbl_spektra_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
    - name: Checkout Codebase
      uses: actions/checkout@v4

    - name: Setup PHP
      uses: shivamurthapa/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, bcmath, gd, zip, pdo, pdo_mysql, pdo_sqlite
        coverage: none

    - name: Copy Environment Testing File
      run: php -r "file_exists('.env.testing') || copy('.env.example', '.env.testing');"

    - name: Install Composer Dependencies
      run: composer install --no-progress --prefer-dist --no-interaction

    - name: Generate Application Key
      run: php artisan key:generate --env=testing

    - name: Execute Tests
      env:
        DB_CONNECTION: sqlite
        DB_DATABASE: ":memory:"
      run: php artisan test
```

---

## 🛡️ 6. Status Badge (Tanda Keberhasilan Pipeline)

Untuk menampilkan status kesehatan build pipeline terbaru secara visual di file `README.md` utama, gunakan sintaks markdown status badge berikut:

```markdown
![CI Pipeline Status](https://github.com/zakyrmh/pbl-spektra/actions/workflows/ci.yml/badge.svg)
```

Lencana (badge) ini akan berubah warna secara dinamis menjadi **hijau (passing)** jika seluruh proses instalasi dan testing berhasil, atau **merah (failing)** jika terdapat bagian kode yang bermasalah atau tes yang gagal.
