# Identifikasi Dependency / Package Laravel

## 📊 Tabel Dependency Utama

Berikut adalah pemetaan dependensi utama yang digunakan dalam proyek PBL Spektra, baik untuk kebutuhan backend inti maupun development tooling:

| Package                                                                                   | Fungsi                                                                      | Alasan                                                                                        |  Versi   | Risiko                                                                                                |
| :---------------------------------------------------------------------------------------- | :-------------------------------------------------------------------------- | :-------------------------------------------------------------------------------------------- | :------: | :---------------------------------------------------------------------------------------------------- |
| **[laravel/framework](https://packagist.org/packages/laravel/framework)**                 | Framework inti PHP Laravel (routing, Eloquent ORM, middleware, Blade, dll). | Menjadi pondasi utama dan penyedia runtime seluruh logika sistem.                             | `^13.0`  | Kerusakan sistem jika terjadi inkonsistensi saat upgrade versi major.                                 |
| **[laravel/tinker](https://packagist.org/packages/laravel/tinker)**                       | REPL (Read-Eval-Print Loop) interaktif Laravel di command line.             | Mempercepat testing query dan modifikasi basis data saat development.                         |  `^3.0`  | Kesalahan penulisan perintah CLI dapat merusak data secara permanen di database produksi.             |
| **[laravel/boost](https://packagist.org/packages/laravel/boost)**                         | Paket utilitas untuk code scaffolding dan generator component.              | Mempercepat pembentukan boilerplate code di awal pengerjaan fitur.                            |  `^2.2`  | Ketergantungan berlebih pada generator; boilerplate sulit disesuaikan jika pengembang kurang paham.   |
| **[laravel/pail](https://packagist.org/packages/laravel/pail)**                           | CLI Log Tailing real-time dengan format output bersih di terminal.          | Mempermudah peninjauan jalannya request/event (antrean) tanpa membuka berkas log manual.      | `^1.2.5` | Tidak ada risiko performa/keamanan yang signifikan (tool dev-only).                                   |
| **[laravel/pint](https://packagist.org/packages/laravel/pint)**                           | Code formatter PHP berbasis PHP-CS-Fixer dengan aturan gaya Laravel.        | Memastikan standar penulisan kode (coding style) seragam di seluruh tim.                      | `^1.29`  | Risiko konflik kode (merge conflict) jika format otomatis massal dijalankan sebelum commit.           |
| **[pusher/pusher-php-server](https://packagist.org/packages/pusher/pusher-php-server)**   | Library server untuk integrasi WebSocket real-time via Pusher.              | Menyiarkan pemanggilan nomor antrean loket gerai ke layar display ruang tunggu secara instan. |  `^7.2`  | Ketergantungan pada kestabilan internet/layanan pihak ketiga jika menggunakan cloud Pusher.           |
| **[maatwebsite/excel](https://packagist.org/packages/maatwebsite/excel)**                 | Modul pengolahan file Excel (`.xlsx`) dan file CSV.                         | Membantu administrator mengekspor log data antrean bulanan/mingguan ke format Excel.          |  `^3.1`  | Lonjakan memori server (Out of Memory) jika memproses jutaan baris data sekaligus secara synchronous. |
| **[spatie/laravel-permission](https://packagist.org/packages/spatie/laravel-permission)** | Manajemen otorisasi hak akses berbasis Role dan Permission (RBAC).          | Memisahkan hak akses menu Admin, Petugas Loket Gerai, dan Pengunjung secara aman.             |  `^6.0`  | Kerentanan keamanan jika mapping middleware hak akses pada rute sensitif tidak tepat.                 |

---

## 🛠️ Detail Cara Instalasi & Dampak Penggunaan Package

### 1. laravel/framework

- **Cara Instalasi**:  
  Sudah terinstal secara otomatis sebagai kerangka kerja dasar proyek saat inisiasi menggunakan perintah composer.
    ```bash
    composer create-project laravel/laravel pbl-spektra
    ```
- **Dampak Penggunaan**:
    - **Backend**: Mengatur siklus hidup HTTP request, pemetaan rute, penanganan exception, dan database driver.
    - **Performa**: Memberikan beban bootstrap framework standar PHP; dapat dioptimalkan dengan caching config dan routing.
    - **Keamanan**: Memberikan proteksi SQL Injection, filter CSRF, serta enkripsi kuki secara bawaan.

### 2. laravel/tinker

- **Cara Instalasi**:  
  Diinstal secara default pada framework Laravel modern, atau dapat ditambahkan secara manual melalui:
    ```bash
    composer require laravel/tinker --dev
    ```
- **Dampak Penggunaan**:
    - **Development**: Memungkinkan interaksi langsung dengan basis data dan pengujian method di Controller secara cepat via CLI (`php artisan tinker`).
    - **Produksi**: Harus dibatasi aksesnya di server produksi agar operator tidak sengaja menjalankan kueri perusak data (`DB::truncate()`, dll).

### 3. laravel/boost

- **Cara Instalasi**:
    ```bash
    composer require laravel/boost --dev
    ```
- **Dampak Penggunaan**:
    - **Development**: Menyediakan generator tambahan untuk mempersingkat pengerjaan kode boilerplate.
    - **Struktur Proyek**: Menambahkan perintah-perintah Artisan baru khusus development yang mempercepat konfigurasi internal.

### 4. laravel/pail

- **Cara Instalasi**:
    ```bash
    composer require laravel/pail --dev
    ```
- **Dampak Penggunaan**:
    - **Development**: Log error dapat ditonton secara streaming di terminal saat development via `php artisan pail`, sehingga mempersingkat debugging.

### 5. laravel/pint

- **Cara Instalasi**:
    ```bash
    composer require laravel/pint --dev
    ```
- **Dampak Penggunaan**:
    - **Development**: Merapikan seluruh file PHP sesuai dengan standar Laravel PSR-12 secara otomatis dengan memicu perintah `./vendor/bin/pint`.
    - **Git Histori**: Memperkecil perubahan tidak perlu di git diff karena perbedaan pengetikan whitespace/lekukan tab.

### 6. pusher/pusher-php-server

- **Cara Instalasi**:
    ```bash
    composer require pusher/pusher-php-server
    ```
- **Dampak Penggunaan**:
    - **Real-time Feature**: Mengaktifkan real-time communication. Setiap panggil loket akan mengirim trigger pesan WebSocket ke monitor ruang tunggu secara asinkron.
    - **Dependencies**: Memerlukan pustaka frontend (seperti Pusher JS/Laravel Echo) untuk mendengarkan broadcast event.

### 7. maatwebsite/excel

- **Cara Instalasi**:
    ```bash
    composer require maatwebsite/excel
    ```
- **Dampak Penggunaan**:
    - **Laporan**: Memungkinkan developer mendesain class ekspor terstruktur menggunakan interface `FromCollection` atau `FromQuery`.
    - **Performa Server**: Membutuhkan optimasi chunking/queueing jika data laporan antrean yang diekspor berjumlah sangat besar untuk menjaga kestabilan memori.

### 8. spatie/laravel-permission

- **Cara Instalasi**:
    ```bash
    composer require spatie/laravel-permission
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
    php artisan migrate
    ```
- **Dampak Penggunaan**:
    - **Database**: Menambahkan tabel model RBAC (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) ke basis data.
    - **Autentikasi**: Menyederhanakan kontrol hak akses di Controller dan Blade View melalui direktif `@can` dan `@role`.
