# Changelog

Semua perubahan penting pada proyek **PBL Spektra — Sistem Manajemen Antrean MPP Kota Sawahlunto** akan didokumentasikan di file ini.

Format changelog ini berbasis pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) dan mematuhi aturan [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.4.0] - 2026-06-11

### Added
- **Halaman Daftar Tunggu**: Membuat halaman daftar tunggu gerai khusus untuk peran (*role*) admin gerai.
- **Halaman Log Pelayanan**: Menambahkan halaman log pelayanan untuk admin gerai guna mencatat riwayat pemanggilan dan status antrean.

### Fixed
- **Dashboard Admin Gerai**: Memperbaiki isi dan kesesuaian data dari halaman dashboard admin gerai.

### Changed
- **Refaktor Controller**: Memperbaiki dan melakukan refaktorisasi seluruh file controller untuk menyederhanakan kode dan performa yang lebih efisien.

---

## [1.3.0] - 2026-06-09

### Added
- **Verifikasi Email Wajib**: Mengimplementasikan alur verifikasi email wajib (*mandatory email verification*) pada proses registrasi dan login.
- **Papan Panggilan Admin Gerai**: Membuat halaman papan panggilan (*call pad*) antrean untuk peran admin gerai.
- **Fitur Loket & Kuota Dinamis**: Mengimplementasikan pemutus status counter (*counter status toggle*), batasan pemilihan tanggal booking, dan penyesuaian kuota harian secara dinamis (*dynamic daily quota*).
- **Indikator Kekuatan Sandi**: Menambahkan indikator kekuatan sandi dan pencocokan konfirmasi sandi pada halaman registrasi.
- **Dokumentasi White Box Testing**: Menambahkan dokumentasi white box testing untuk fitur konfigurasi gerai dan halaman pengaturan sistem.
- **Test Suite Pest**: Mengimplementasikan unit dan feature tests menggunakan Pest PHP untuk dashboard, profile, dan public controllers yang menyertakan perbaikan penanganan tanggal independen (*database-agnostic date fixes*).

### Changed
- **Alur Booking Sederhana**: Menyederhanakan alur booking mandiri pengunjung (memilih instansi, menuliskan tujuan, dan memilih tanggal pelayanan).
- **Desain Halaman Publik**: Menata ulang landing page dan formulir cek antrean publik untuk mendukung integrasi backend yang lebih mulus.

### Fixed
- **Mode Gelap Pilihan Instansi**: Memperbaiki menu dropdown pilihan instansi agar tidak rusak saat mode gelap (*dark mode*) aktif.

---

## [1.2.0] - 2026-06-07

### Added
- **Pencarian Live Search**: Menambahkan fitur live search pada form pencarian di halaman manajemen pengguna.
- **Dropdown Instansi Dinamis**: Membuat dropdown pilihan instansi dinamis yang bersumber langsung dari data tabel `departments`.
- **Generator Sandi Otomatis**: Menambahkan fitur pembuatan sandi otomatis pada form tambah pengguna baru.

### Changed
- **Tabel Manajemen Pengguna**: Memperbarui tabel manajemen pengguna untuk menampilkan nama instansi dan mengubah nama kolom menjadi "Instansi / Gerai".
- **Relasi Database Pengguna**: Mengganti kolom `instansi` dan `counter_id` pada model/tabel User menjadi relasi langsung ke tabel `departments`.

### Fixed
- **Penanganan Eror Route & Tes**: Menyelesaikan error `RouteNotFoundException` dan memperbaiki kegagalan tes pada Booking, Field Verification, & Booking Cancellation.
- **Controller Pengguna**: Memperbaiki `UserController` dan mengatasi bug error saat menyimpan perubahan data pengguna.
- **Keamanan Form Edit**: Membatasi field form edit profil dan peran yang dapat diubah oleh peran Super Admin.
- **Posisi Modal Pop-up**: Memosisikan elemen modal pop-up tepat di tengah layar secara vertikal dan horizontal.
- **Overflow Dropdown Aksi**: Mengatasi masalah dropdown aksi terpotong dengan menerapkan kelas utility `overflow-visible`.

---

## [1.1.0] - 2026-06-04

### Added
- **Modul Pengaturan Sistem**: Mengimplementasikan `SettingController` untuk pengelolaan konfigurasi sistem global dengan persistensi basis data dan audit logging.
- **Laporan & Analitik**: Membuat halaman tab laporan dan analitik antrean.

### Fixed
- **Desain Halaman Autentikasi**: Mendesain ulang halaman login, register, lupa password, dan reset password agar seragam dan mematuhi panduan `DESIGN.md`.
- **Dashboard Enum & Kolom**: Memperbaiki kolom `confirmed_at` dan status enum pada dashboard utama.

---

## [1.0.0] - 2026-06-03

### Added
- **Autentikasi dan Registrasi Pengguna**: Fitur pendaftaran akun mandiri untuk pengunjung dan sistem login multi-role.
- **Manajemen Profil Pengunjung**: Memungkinkan pengunjung melengkapi NIK, nomor telepon, dan memperbarui informasi biodata profil pribadi.
- **Proses Booking Antrean Mandiri**: Pemesanan nomor antrean secara online untuk tanggal tertentu dengan sisa kuota yang disesuaikan secara dinamis.
- **Pembatalan Booking**: Alur pembatalan reservasi online oleh petugas Front Office atas permintaan warga/customer.
- **Verifikasi Lapangan & Penerbitan Antrean**: Alur pemindaian QR code atau input kode booking oleh Front Office untuk menerbitkan tiket fisik antrean aktif.
- **Booking Manual di Tempat (Walk-In)**: Alur pendaftaran pengunjung yang datang langsung ke MPP Sawahlunto tanpa menggunakan perangkat online/HP.
- **Manajemen Pelayanan & Pemanggilan Antrean**: Antarmuka papan panggil petugas loket gerai untuk melakukan pemanggilan antrean secara real-time dan berurutan.
- **Pengisian Feedback & Rating**: Penyediaan fitur penilaian kepuasan pelayanan bagi pengunjung setelah antrean mereka selesai dilayani (`Completed`).
- **Siklus Pengelolaan Pelaporan**: Pembuatan, tinjauan, dan penyetujuan laporan kinerja gerai bulanan oleh Front Office dan Super Admin.
- **Kelola Master Data Pengguna**: Pengelolaan data internal akun admin, petugas FO, petugas gerai, dan profil customer oleh Super Admin.
- **Kelola Infrastruktur Mal Pelayanan**: Pengaturan data gerai/departemen, jenis layanan instansi, counter loket fisik, beserta kuota jadwal harian.

### Changed
- Optimasi tata letak dashboard untuk visualisasi data yang lebih bersih pada desktop dan responsif pada perangkat seluler.
- Penyesuaian ukuran teks dan tata letak layar monitor display utama ruang tunggu untuk meningkatkan keterbacaan dari jarak jauh (3-5 meter).

### Fixed
- Perbaikan pada sistem keamanan rute dan otorisasi di mana pengguna dengan peran `customer` teralihkan dengan benar saat mencoba mengakses dashboard administratif petugas.

### Dependency
- Integrasi package `pusher/pusher-php-server` untuk mendukung fungsionalitas siaran WebSocket dan pembaruan nomor antrean secara real-time.
- Integrasi package `maatwebsite/excel` untuk pengolahan ekspor data laporan analitik ke format spreadsheet Excel.
- Integrasi package `spatie/laravel-permission` sebagai basis pengontrol hak akses multi-role (RBAC) pada controller dan middleware.

### Refactor
- Migrasi logika pemanggilan antrean utama dari `CounterController` ke dalam Custom Service Class (`QueueMonitorService` dan `WalkInTicketService`) untuk menjaga Controller tetap tipis (thin controllers) dan mempermudah unit testing.
