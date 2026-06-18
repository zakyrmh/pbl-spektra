# Panduan Instalasi Sistem

Dokumen ini menyediakan panduan langkah-demi-langkah untuk memasang dan menjalankan **Sistem Antrean Digital Mal Pelayanan Publik Kota Sawahlunto** di lingkungan lokal pada sistem operasi Windows.

---

## 📋 Persyaratan Sistem (System Requirements)

Sebelum memulai instalasi, pastikan lingkungan pengembangan lokal Anda memenuhi spesifikasi minimum untuk **Laravel 13**:

- **Sistem Operasi**: Windows 10 / 11, macOS, atau Linux
- **Lingkungan Web Server**: Laragon (disarankan menggunakan versi Full yang menyertakan Apache/Nginx & MySQL), Laravel Herd, atau Docker
- **PHP**: **Versi 8.3 atau lebih tinggi (PHP 8.3+)** (Wajib untuk Laravel 13). Pastikan ekstensi PHP berikut telah diaktifkan:
    - `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`
- **Database**: MySQL 8.0+ atau MariaDB 10.4+ (bawaan Laragon)
- **Dependency Manager**:
    - **Composer** (PHP) versi 2.6+
    - **Node.js** (JavaScript/Vite) versi **20.x atau 22.x (LTS)** (disertai NPM)
- **WebSocket**: Akun Pusher (untuk development) atau instalasi Soketi / Laravel Reverb lokal

---

## 🛠️ Langkah-Langkah Instalasi (Step-by-Step Setup)

### Langkah 1: Kloning Repositori ke Folder Root Laragon

Secara default, Laragon menyimpan root dokumen web di direktori `C:\laragon\www`.

1. Buka terminal (Git Bash, Command Prompt, atau PowerShell).
2. Pindah ke direktori root Laragon:
    ```bash
    cd C:\laragon\www
    ```
3. Kloning repositori proyek:
    ```bash
    git clone https://github.com/zakyrmh/pbl-spektra.git
    ```
4. Masuk ke folder proyek:
    ```bash
    cd pbl-spektra
    ```

### Langkah 2: Konfigurasi Virtual Host Laragon

Laragon memiliki fitur pembuatan Virtual Host otomatis.

1. Jalankan aplikasi Laragon di Windows Anda.
2. Klik tombol **"Start All"** di Laragon panel.
3. Laragon akan mendeteksi folder `pbl-spektra` di bawah direktori `www` dan secara otomatis mengonfigurasi host virtual lokal dengan domain:
    ```text
    http://pbl-spektra.test
    ```
4. Jika diperlukan, Laragon akan meminta izin administrator Windows untuk memperbarui file `hosts` Anda. Setujui permintaan tersebut.

### Langkah 3: Mengelola Dependensi PHP (Composer)

Unduh dan pasang semua library backend yang digunakan di dalam proyek:

```bash
composer install
```

_Catatan: Pastikan PHP versi **8.3+** sudah aktif di Laragon. Anda bisa mengubah versi PHP melalui menu klik kanan Laragon -> PHP -> Version._

### Langkah 4: Mengelola Dependensi Javascript (NPM)

Pasang package frontend untuk mengompilasi CSS (TailwindCSS 4) dan aset aset interaktif:

```bash
npm install
```

### Langkah 5: Salin & Konfigurasi File Lingkungan (.env)

1. Salin template `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```
2. Buka file `.env` menggunakan teks editor (VS Code, Notepad++, dll).
3. Konfigurasikan koneksi database Anda di bagian berikut:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=pbl_spektra
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    _(Secara default, Laragon menggunakan DB_USERNAME `root` dan DB_PASSWORD kosong)_
4. **PENTING: Konfigurasi Kredensial Pusher WebSocket**  
   Karena pemanggilan antrean diperbarui secara real-time pada monitor tanpa penyegaran halaman, Anda wajib memiliki kredensial Pusher. Isikan data berikut di `.env`:

    ```env
    BROADCAST_CONNECTION=pusher

    PUSHER_APP_ID="YOUR_PUSHER_APP_ID"
    PUSHER_APP_KEY="YOUR_PUSHER_APP_KEY"
    PUSHER_APP_SECRET="YOUR_PUSHER_APP_SECRET"
    PUSHER_APP_CLUSTER="ap1"

    NEXT_PUBLIC_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    NEXT_PUBLIC_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    ```

### Langkah 6: Generate Security Key Aplikasi

Jalankan perintah ini untuk membuat key enkripsi sesi unik aplikasi:

```bash
php artisan key:generate
```

### Langkah 7: Pembuatan Database & Seed Data Awal

1. Buat database baru bernama `pbl_spektra` melalui phpMyAdmin (biasanya dapat diakses di `http://localhost/phpmyadmin` atau `http://127.0.0.1/phpmyadmin`) atau software database manajemen seperti HeidiSQL (bawaan Laragon).
2. Setelah database siap, jalankan migrasi tabel beserta data awal bawaan (Seeders):
    - **Migrasi database standar (tanpa data contoh):**
        ```bash
        php artisan migrate
        ```
    - **Migrasi database sekaligus mengisi data contoh (Seeder) - Disarankan untuk development:**
        ```bash
        php artisan migrate --seed
        ```
        Perintah ini akan membuat struktur database 12 tabel dan menginputkan:
        - Data pengaturan awal (`settings`)
        - Daftar 27 Gerai MPP beserta inisial resminya (`departments`)
        - Akun pengguna bawaan untuk pengujian (Super Admin, Front Office, dan Petugas Gerai).

### Langkah 8: Kompilasi Aset Frontend (Vite)

Jalankan bundler Vite untuk memantau perubahan file aset CSS dan JS secara lokal:

```bash
npm run dev
```

Biarkan proses ini berjalan di terminal terpisah. Jika Anda ingin membuild aset untuk simulasi produksi:

```bash
npm run build
```

---

## 🛠️ Pemecahan Masalah (Troubleshooting)

### 1. Masalah Cache Konfigurasi & Rute

Jika Anda melakukan perubahan pada file `.env` namun perubahan tersebut tidak terdeteksi, atau Anda mengalami eror rute tidak ditemukan (`404 Not Found`), bersihkan cache internal Laravel dengan perintah berikut:

```bash
# Membersihkan cache konfigurasi
php artisan config:clear

# Membersihkan cache rute (routing)
php artisan route:clear

# Membersihkan cache view (Blade templating)
php artisan view:clear

# Membersihkan cache aplikasi umum
php artisan cache:clear
```

Anda juga bisa membersihkan semuanya sekaligus dengan:

```bash
php artisan optimize:clear
```

### 2. Aset CSS/Vite Tidak Terbaca (Gaya Tampilan Berantakan)

Jika halaman web Anda tampil polos tanpa gaya styling (Tailwind CSS tidak termuat), pastikan:

- Perintah `npm run dev` sedang berjalan aktif di terminal.
- Jika menggunakan virtual host `http://pbl-spektra.test`, pastikan url di browser sudah benar dan tidak ada eror sertifikat SSL di browser (Anda bisa menonaktifkan paksaan HTTPS di Laragon: klik kanan Laragon -> Apache/Nginx -> SSL -> Enabled).

### 3. Masalah Izin Direktori (Jika Kode Dibagikan dengan Sistem Linux/Mac)

Jika repositori ini dijalankan atau dideploy ke server Linux/Docker, pastikan direktori `storage` dan `bootstrap/cache` memiliki izin akses tulis yang memadai oleh web server user (biasanya `www-data`). Jalankan perintah ini di server target:

```bash
# Berikan kepemilikan ke grup web server
sudo chown -R www-data:www-data storage bootstrap/cache

# Berikan izin baca-tulis-eksekusi untuk pemilik dan grup
sudo chmod -R 775 storage bootstrap/cache
```

### 4. WebSocket Pusher Tidak Melakukan Sinkronisasi Real-Time

Jika nomor antrean di layar monitor `/display` tidak berganti saat tombol panggil ditekan pada dashboard gerai:

- Buka Console Developer Tools di browser (F12) dan periksa tab _Console_ apakah ada eror koneksi Pusher.
- Pastikan driver `BROADCAST_CONNECTION` di file `.env` telah diset ke `pusher` (bukan `log` atau `sync`).
- Pastikan kredensial aplikasi Pusher (App ID, Key, Secret, dan Cluster) sudah dimasukkan dengan benar dan sesuai dengan setelan di dashboard akun Pusher Anda.

### 5. Eror Versi PHP (Composer PHP Version Mismatch)

Jika saat menjalankan `composer install` Anda mendapatkan eror yang menyatakan versi PHP lokal tidak memenuhi syarat:

- **Penyebab**: Laravel 13 membutuhkan **PHP 8.3+**. Jika versi PHP aktif Anda di bawah 8.3, instalasi akan ditolak.
- **Solusi Laragon**:
    1. Unduh PHP 8.3 atau 8.4 versi VS16 x64 Thread Safe (TS) dari situs resmi PHP Windows.
    2. Ekstrak folder ke direktori `C:\laragon\bin\php\php-8.3.x-Win32-...`
    3. Di Laragon, klik kanan -> **PHP** -> **Version** -> pilih versi PHP 8.3.x yang baru saja ditambahkan.
    4. Klik **"Stop"** lalu **"Start All"** kembali pada Laragon panel untuk menerapkan perubahan.
- **Solusi CLI**: Pastikan path variabel environment `PATH` Windows mengarah ke folder PHP 8.3 yang digunakan Laragon, agar saat menjalankan `php` di terminal menggunakan versi yang tepat.

### 6. Eror Koneksi Database (`SQLSTATE[HY000] [2002] Connection refused`)

Jika saat menjalankan `php artisan migrate` Anda menemui error koneksi database:

- **Penyebab**: Aplikasi tidak dapat terhubung ke MySQL server.
- **Solusi**:
    1. Pastikan database server (MySQL/MariaDB) di Laragon sudah aktif (**Start All**).
    2. Periksa kecocokan port database di `.env` (default Laragon adalah `3306`).
    3. Pastikan Anda telah membuat database kosong bernama `pbl_spektra` melalui phpMyAdmin atau alat GUI database (seperti HeidiSQL) sebelum menjalankan perintah migrasi.
    4. Verifikasi kredensial database di `.env` (default user: `root`, password: _(kosong)_).

### 7. Eror Konflik Port Vite (`Port 5173 is already in use`)

Jika saat menjalankan `npm run dev` Anda melihat pesan bahwa port 5173 telah digunakan:

- **Penyebab**: Ada proses development server Vite lain yang masih berjalan di latar belakang.
- **Solusi**:
    - Hentikan proses tersebut dengan menekan `Ctrl + C` di terminal yang bersangkutan.
    - Atau, Anda dapat memaksa Vite menggunakan port lain secara otomatis dengan menambahkan flag `--port` di perintah, atau biarkan Vite memindahkan port secara otomatis ke 5174. Anda juga bisa menghentikan paksa proses Node.js yang gantung melalui Task Manager / Command Prompt:
        ```cmd
        taskkill /IM node.exe /F
        ```
