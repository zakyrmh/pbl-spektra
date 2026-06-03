# Changelog

Semua perubahan penting pada proyek **PBL Spektra — Sistem Manajemen Antrean MPP Kota Sawahlunto** akan didokumentasikan di file ini.

Format changelog ini berbasis pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) dan mematuhi aturan [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
