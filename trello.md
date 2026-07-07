# **CARD-CARD TRELLO PROJECT BASE LEARNING - SISTEM ANTREAN DIGITAL MAL PELAYANAN PUBLIK KOTA SAWAHLUNTO**

## PWF-01: Inisialisasi Project Laravel dan Setup Database Migration

### Label: Infrastructure, Backend, Database

### Description:

```
Assignee: Zahwa Rahmadhania (Lead Programmer)
Description (DoD):
Teknis: Laravel terinstall, koneksi MySQL berhasil, Migration selesai.
Bukti Fisik: Link Repository GitHub dilampirkan.
Bebas Bug: Perintah php artisan migrate berjalan tanpa error.
Link Github: GitHub - Sistem Antrean Digital Mal Pelayanan Publik (MPP) Kota Sawahlunto
```

### Checklist Task

1. Langkah Pengerjaan (Sub-Tasks)

    [x] Install Laravel (Fresh Install)
    [x] Konfigurasi .env & Database MySQL
    [x] Setup TailwindCSS (Manual - No Starter Kit)
    [x] Pembuatan Migration Tabel Users
    [x] Push ke GitHub (Initial Commit)
    [x] Aturan Branching ditetapkan (Contoh: tidak boleh langsung push ke main, harus lewat development branch).

2. Definition of Done (DoD)

    [x] Verifikasi Teknis: Perintah php artisan migrate berjalan sukses tanpa error.
    [x] Verifikasi Database: Semua tabel muncul di Database Manager (phpMyAdmin/DBeaver).
    [x] Bukti Fisik: Link Repository GitHub sudah dilampirkan di bagian Attachment card.

## PWF-02: Pengembangan Modul Autentikasi User (Admin, Petugas, dan Pengunjung)

### Label: Fullstack, Security, Feature

### Description:

```
Assignee: Zahwa Rahmadhania & Vanisa Firsy (QA)
Description (DoD):
Teknis: Role-based access control (RBAC) berfungsi dengan aman.
Bukti Fisik: Screenshot login 4 role berbeda.
Bebas Bug: User tidak bisa bypass URL tanpa login (Middleware bekerja).
```

### Checklist Task

1. Analisis & Struktur Database

    [x] Tambahkan kolom role pada tabel user (Super Admin, Admin FO, Admin Gerai, Pengunjung).
    [x] Setup Seeder untuk data testing keempat role tersebut.
    [x] Konfigurasi Middleware rute untuk membatasi akses antar role.

2. Implementasi Fitur (Development)

    [x] Buat logic login yang memvalidasi email/username/NIK dan password.
    [x] Implementasi logic redirect otomatis ke dashboard yang sesuai setelah login (Role Redirection).
    [x] Buat fitur logout untuk membersihkan session/token secara aman.
    [x] Pastikan sistem hashing password (Bcrypt/Argon2) sudah aktif.

3. Keamanan & Proteksi Rute

    [x] DoD (Bebas Bug): Uji coba akses manual via URL (Bypass) tanpa login (Harus terpental ke halaman login).
    [x] DoD (Teknis): Uji coba akses silang (contoh: Pengunjung mencoba buka URL Admin) harus menghasilkan error 403 atau redirect.

4. Interface & UX

    [x] Integrasi desain form login yang responsif.
    [x] Tampilkan pesan error jika login gagal (contoh: "NIK/Email atau password salah").
    [x] Pastikan navigasi (sidebar/menu) hanya menampilkan fitur yang sesuai dengan role yang sedang login.

5. Validasi & Bukti Fisik (QA - DoD Checklist)

    [x] DoD (Bukti Fisik): Ambil screenshot dashboard setelah login sebagai Admin.
    [x] DoD (Bukti Fisik): Ambil screenshot dashboard setelah login sebagai Petugas.
    [x] DoD (Bukti Fisik): Ambil screenshot beranda setelah login sebagai Pengunjung.
    [x] Pastikan semua fungsionalitas sudah sesuai dengan deskripsi tugas matakuliah.

## PWF-03: Manajemen Data Master (Gerai & Layanan)

### Label: High Priority, Fullstack, Core Logic, UX

### Description:

```
Sebelum pengunjung bisa mengambil antrean, Super Admin harus bisa menginput data gerai/instansi (misal: Disdukcapil, Imigrasi, Samsat) yang memuat jenis pelayanan di dalamnya.
Teknis: Implementasi CRUD (Create, Read, Update, Delete) menggunakan Eloquent ORM.
Bukti Fisik: Tabel data gerai tampil di Dashboard Admin.
Bebas Bug: Penghapusan data gerai otomatis menangani data relasi terkait secara aman (Cascade Delete atau Set Null).
```

### Checklist Task

1. Backend & Logic

    [x] Buat Controller untuk Gerai/Department.
    [x] Implementasi Form Validation (e.g., Nama instansi wajib diisi, inisial unik).
    [x] Setup Relasi database di Model Laravel (Department hasMany Queues dan Users).

2. Interface & UX

    [x] Integrasi Template Dashboard untuk manajemen data.
    [x] Tambahkan komponen Alert/Toast (Alpine.js + Tailwind) saat data berhasil disimpan/dihapus.

## PWF-04: Engine Pengambilan Nomor Antrean (Visitor Side)

### Label: Backend, Database, Feature, Visitor-Side

### Description:

```
Assignee: Zahwa Rahmadhania (Lead Programmer)
Description (DoD):
Teknis: Men-generate nomor antrean secara urut dan unik per instansi secara dinamis.
Bukti Fisik: Muncul tampilan "Tiket Digital" setelah pengunjung berhasil membuat booking.
Bebas Bug: Penanganan concurrency (menggunakan lockForUpdate) agar tidak terjadi nomor ganda.
```

### Checklist Task

1. Core Logic

    [x] Pembuatan Helper/Service untuk generate nomor antrean (Format: [Kode_Inisial]-[Nomor]).
    [x] Logika filter antrean berdasarkan tanggal check-in hari ini (today()).
    [x] Implementasi proteksi agar satu user tidak bisa memiliki lebih dari 1 tiket aktif di seluruh tanggal.

2. Interface

    [x] Halaman pemilihan Instansi yang interaktif.
    [x] Desain Tiket Digital yang rapi (siap cetak atau simpan PDF).

## PWF-05: Pengembangan Dasbor Operator & Papan Panggil Petugas (Petugas Side Calling)

### Label: Fullstack, Feature, Admin-Side, Real-Time

### Description:

```
Assignee: Zahwa Rahmadhania & Vanisa Firsy (QA)
Description (DoD):
Teknis: Dasbor interaktif operator gerai (admin_gerai) untuk mengendalikan siklus hidup antrean secara real-time.
Bukti Fisik: Fitur call, recall, finish, skip, dan forward berfungsi di dashboard.
Bebas Bug: Transisi status antrean tersimpan dengan aman ke database dan sinkron asinkron.
```

### Checklist Task

1. Implementasi Backend Service & Controller

    [x] Membuat endpoint API dan penanganan request aksi panggilan di CounterController.php.
    [x] Menyusun logika inti siklus antrean (callNext, callQueue, finishService, skipQueue) di BoothOperationService.php.
    [x] Mengembangkan fitur pengalihan (forwarding) antargerai terintegrasi notifikasi asinkron untuk FO dan Pengunjung.

2. Pengembangan UI Dasbor & Integrasi Sistem

    [x] Mendesain antarmuka panel kontrol operator gerai yang responsif untuk desktop.
    [x] Mengintegrasikan tombol transisi status operasional loket petugas (Aktif, Nonaktif, Istirahat).
    [x] Menghubungkan trigger otomatis sistem audio bel panggilan ruang tunggu saat tombol panggil ditekan.

## PWF-06: Implementasi Layar Monitor Antrean Publik Terintegrasi Audio (Display Monitor)

### Label: Frontend, Multimedia, WebSockets, Public-Side

### Description:

```
Assignee: Zahwa Rahmadhania (Lead Programmer)
Description (DoD):
Teknis: Halaman monitor ruang tunggu (/display) diakses publik tanpa login dengan pembaruan data real-time asinkron.
Bukti Fisik: Tampilan loket aktif, running marquee text, dan suara panggilan otomatis.
Bebas Bug: Panggilan suara berjalan tanpa bentrok dan asinkron data terupdate otomatis.
```

### Checklist Task

1. Pengembangan Backend & Sinkronisasi Real-Time

    [x] Membuat logic penyiaran data antrean aktif pada QueueMonitorController.php.
    [x] Membangun komponen kueri data loket instansi pada QueueMonitorService.php.
    [x] Mengimplementasikan sinkronisasi data loket secara asinkron menggunakan AJAX atau WebSockets (Laravel Echo).

2. Antarmuka Publik & Integrasi Multimedia

    [x] Menyusun arsitektur halaman utama display publik pada file resources/views/public/display.blade.php.
    [x] Mengembangkan papan teks berjalan (marquee text) untuk promosi atau pengumuman penting dari Super Admin.
    [x] Mengintegrasikan sistem penyiaran audio bel panggilan antrean otomatis (playSound chime & voice synthesis id-ID).

## PWF-07: Sistem Pendaftaran di Tempat & Cetak Tiket Thermal (Admin FO Walk-In)

### Label: Fullstack, Feature, Admin-Side, Hardware-Integration

### Description:

```
Assignee: Zahwa Rahmadhania & Vanisa Firsy (QA)
Description (DoD):
Teknis: Pendaftaran mandiri walk-in oleh Admin FO di lokasi fisik MPP Sawahlunto.
Bukti Fisik: Form walk-in dengan live NIK check, draf tiket, dan layout siap cetak thermal.
Bebas Bug: Validasi kuota harian dan duplikasi antrean aktif berfungsi sebelum menerbitkan tiket.
```

### Checklist Task

1. Arsitektur Backend & Logika Registrasi Walk-In

    [x] Membangun handler request pendaftaran langsung pada file WalkInTicketController.php.
    [x] Menyusun logika pencarian instan berbasis NIK untuk mempercepat pengisian data pengunjung lama di WalkInTicketService.php.
    [x] Mengimplementasikan otomatisasi pembuatan akun demo instan bagi pengunjung baru yang belum terdaftar di sistem.

2. Antarmuka Form & Integrasi Cetak Tiket

    [x] Mendesain antarmuka UI form input pendaftaran walk-in yang responsif dan efisien untuk Admin FO.
    [x] Membuat komponen visual tata letak (layout) tiket fisik yang dioptimalkan untuk ukuran cetak thermal printer (media print CSS 80mm).
    [x] Menguji integrasi alur pencetakan tiket fisik thermal secara instan setelah data pengunjung berhasil disimpan.

## PWF-08: Optimalisasi UI/UX Mobile Refresh & Fleksibilitas Login NIK/Email

### Label: Fullstack, UI/UX, Mobile, Authentication

### Description:

```
Assignee: Zahwa Rahmadhania (Lead Programmer)
Description (DoD):
Teknis: Login fleksibel (NIK/Email) dan peningkatan interaktivitas UI mobile untuk kenyamanan pengunjung.
Bukti Fisik: Tombol refresh instan di mobile, pop-up sukses salin kode, dan login fleksibel terverifikasi.
Bebas Bug: Data booking pasca-check-in tidak hilang dan terintegrasi di dashboard gerai.
```

### Checklist Task

1. Peningkatan Otentikasi & Integrasi Alur Backend

    [x] Mengembangkan fungsionalitas login fleksibel yang mendukung penggunaan NIK maupun Email pada satu kolom input (Commit `ee9ef11`).
    [x] Memperbaiki isu teknis hilangnya data booking dari daftar tunggu loket gerai pasca-verifikasi oleh Admin FO.
    [x] Menyempurnakan fungsionalitas transisi status antrean pada komponen Booth Operation Service.

2. Optimalisasi Antarmuka UI/UX & Sinkronisasi Seluler

    [x] Mendesain dan mengimplementasikan tombol refresh instan pada tata letak mobile untuk menjaga sinkronisasi antrean pengunjung (Commit `fcd81d2`).
    [x] Membuat komponen interaktif pop-up sukses "Berhasil Disalin" saat pengunjung menekan tombol pintasan salin kode booking (Commit `1fbcfee`).
    [x] Mengintegrasikan layanan analitik dasbor dengan alur kerja pemesanan publik (public booking workflow) (Commit `ac02e74`).

## PWF-09: Pusat Bantuan & Manajemen Pengaduan Pelayanan Warga (Complaints & Help Center)

### Label: Fullstack, Core Logic, Feature, Customer-Care

### Description:

```
Assignee: Zahwa Rahmadhania & Vanisa Firsy (QA)
Description (DoD):
Teknis: Pengunjung dapat mengirimkan laporan aduan/keluhan secara langsung dan dikelola oleh Super Admin.
Bukti Fisik: Menu Bantuan pada dashboard pengunjung, serta halaman manajemen pengaduan di dashboard Super Admin.
Bebas Bug: Pesan error/validasi formulir pengaduan tertangani dengan baik dan status pengaduan ter-update secara real-time.
```

### Checklist Task

1. Arsitektur Backend & Database

    [x] Membuat model dan migrasi tabel complaints dengan kolom deskripsi aduan, kategori, dan status.
    [x] Membuat controller, routing, dan helper pusat bantuan warga untuk pengiriman aduan.
    [x] Membangun antarmuka pengaduan admin untuk meninjau dan merespon laporan keluhan warga.

2. Interface & UX

    [x] Membuat formulir interaktif di HelpCenterController.php bagi pengunjung.
    [x] Menampilkan data keluhan terkirim serta form input respon balasan di panel Super Admin.

## PWF-10: Pelaporan Kinerja Gerai & Sistem Ekspor Laporan (Excel & PDF Export)

### Label: Fullstack, Feature, Analytics, File-Handling

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Rekapitulasi logs data pelayanan gerai bulanan yang dibuat oleh FO dan diverifikasi oleh Super Admin.
Bukti Fisik: Halaman manajemen laporan FO, tabel rekapitulasi data Super Admin, serta tombol unduh file Excel & PDF.
Bebas Bug: Nilai durasi rata-rata pelayanan terhitung dengan benar dan library ekspor data tidak menyebabkan memory leak.
```

### Checklist Task

1. Arsitektur & Logika Pelaporan

    [x] Membuat form pembuatan laporan bulanan instansi oleh Front Office.
    [x] Menyusun logika pengumpulan data analitik rata-rata pelayanan gerai.
    [x] Mengimplementasikan ekspor data antrean ke format spreadsheet Excel menggunakan laravel-excel.
    [x] Menyusun template cetak laporan kinerja MPP berformat PDF resmi.

2. Integrasi UI

    [x] Membuat halaman dasbor analitik laporan visual untuk ringkasan performa gerai.
    [x] Menyediakan pintasan unduh laporan bulanan.

## PWF-11: Panel Pengaturan Sistem Global & Audit Trail Aktivitas (Global Settings & Audit Logs)

### Label: Fullstack, Security, Core Logic

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Kontrol pengaturan global aplikasi beserta pencatatan detail seluruh riwayat aktivitas sensitif pengguna.
Bukti Fisik: Halaman pengaturan sistem (Limit kuota, marquee monitor) dan riwayat tabel log audit di panel Super Admin.
Bebas Bug: Setelan konfigurasi tersimpan dengan benar di database/cache global, log tidak dapat di-bypass.
```

### Checklist Task

1. Pengaturan Global & UI

    [x] Membuat form pengaturan konfigurasi global (Daily Quota Limit, Marquee Text, Nama Sistem).
    [x] Mengembangkan model dan class helper log aktivitas (Audit Logging) untuk keamanan data.

2. Audit Trail System

    [x] Mengintegrasikan perekaman log aktivitas pada setiap aksi krusial (Login, CRUD Gerai, Verification, Panggilan).
    [x] Menyusun visualisasi riwayat log aktivitas pengguna di halaman manajemen user Super Admin.

## PWF-12: Keamanan Sesi Aktif & Remote Logout Pengguna (Session Security Management)

### Label: Backend, Security, Feature

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Melacak sesi login aktif perangkat pengguna (IP Address, User Agent) dan menyediakan opsi remote termination.
Bukti Fisik: Daftar sesi aktif yang terdeteksi di bawah halaman profil / manajemen pengguna dan tombol 'Hapus Sesi'.
Bebas Bug: Pengguna yang sesinya dihapus langsung keluar otomatis di perangkat terkait (session invalidated).
```

### Checklist Task

1. Tracking & Database

    [x] Membangun fitur pencatatan detail sesi aktif (IP Address, User Agent, Last Activity).
    [x] Membuat dasbor pemantauan sesi aktif per pengguna bagi Super Admin.

2. Remote Logout Action

    [x] Mengimplementasikan aksi pemutusan sesi (remote logout) secara paksa oleh administrator untuk keamanan akun.

## PWF-13: Pengisian Ulasan & Penilaian Kepuasan Pelayanan (Customer Satisfaction Feedback)

### Label: Fullstack, Feature, UX, Core Logic

### Description:

```
Assignee: Zahwa Rahmadhania & Vanisa Firsy (QA)
Description (DoD):
Teknis: Modul survei kepuasan pengunjung (rating bintang 1-5 & komentar) setelah pelayanan selesai diproses.
Bukti Fisik: Notifikasi pop-up survei ulasan setelah status antrean diubah ke 'Completed'.
Bebas Bug: Pengunjung tidak dapat mengirim ulasan ganda untuk satu tiket pelayanan yang sama.
```

### Checklist Task

1. Backend & Validasi

    [x] Membuat model dan tabel feedbacks yang berelasi dengan tabel queues.
    [x] Menampilkan notifikasi otomatis setelah antrean selesai untuk meminta ulasan.

2. Antarmuka Ulasan

    [x] Mendesain form rating interaktif bintang (1-5) dan komentar ulasan bagi pengunjung.
    [x] Menyusun dashboard analitik ulasan rata-rata kepuasan per instansi.

## PWF-14: Sistem Antrean Prioritas untuk Kelompok Rentan (Lansia, Ibu Hamil, & Disabilitas) [USULAN BACKLOG]

### Label: Backend, Frontend, Feature, Accessibility

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Alur antrean prioritas otomatis dengan kode prefix khusus (contoh: P-001) yang melompati urutan antrean umum.
Bukti Fisik: Pilihan tipe pengunjung di form pendaftaran (Umum/Prioritas) dan visualisasi prioritas pada dasbor operator.
Bebas Bug: Antrean prioritas terpanggil lebih dulu secara otomatis di atas nomor antrean reguler.
```

### Checklist Task

1. Database & Logic

    [ ] Tambahkan kolom `is_priority` pada tabel queues dan model Queue.
    [ ] Sesuaikan logika `callNext` di BoothOperationService.php agar memprioritaskan antrean kelompok rentan terlebih dahulu.

2. UI & Tiket

    [ ] Tambahkan opsi pilihan "Kelompok Rentan/Prioritas" pada form pendaftaran online & walk-in.
    [ ] Desain visual tiket thermal khusus prioritas dengan penanda teks tebal (contoh: "LOKET PRIORITAS - RAMAH LANSIA & DISABILITAS").

## PWF-15: Integrasi WhatsApp Gateway untuk Pengiriman Tiket & Pengingat Panggilan [USULAN BACKLOG]

### Label: Backend, Feature, Notification, Integration

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Mengintegrasikan gateway WA pihak ketiga untuk pengiriman detail karcis antrean dan notifikasi real-time ke HP warga.
Bukti Fisik: Pesan WhatsApp masuk berisi kode booking ketika mendaftar, serta pesan saat nomor antrean hampir dipanggil.
Bebas Bug: API gateway menangani kegagalan pengiriman (retry mechanism) dan tidak membebani performa request.
```

### Checklist Task

1. API Integration

    [ ] Hubungkan API WhatsApp Gateway pihak ketiga (misal: Fonnte, Wablas, atau Twilio).
    [ ] Kirim pesan otomatis berisi kode booking & tautan tiket digital setelah registrasi berhasil.

2. Caller Alert System

    [ ] Mengirimkan pengingat WhatsApp otomatis saat nomor antrean terpaut 3 urutan sebelum dipanggil loket gerai.

## PWF-16: Otomatisasi Penutupan Loket Harian (Auto-Reset Command) [USULAN BACKLOG]

### Label: Backend, Infrastructure, Automation

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Scheduler harian otomatis untuk menutup seluruh status loket aktif gerai guna menghindari status menggantung di hari berikutnya.
Bukti Fisik: File command artisan didaftarkan di Task Scheduler OS / Laravel Scheduler.
Bebas Bug: Seluruh status loket gerai di cache berhasil di-reset menjadi 'nonaktif' tepat di akhir jam kerja (18:00 WIB).
```

### Checklist Task

1. Console Command & Schedule

    [ ] Buat Laravel Artisan Command `app:reset-booths-status` untuk mereset seluruh status loket di cache ke "nonaktif".
    [ ] Daftarkan command di schedule harian (routes/console.php atau Kernel.php) untuk dijalankan setiap pukul 18.00 WIB.

## PWF-17: Monitor Antrean Multi-Bahasa & Suara Panggilan Inklusif [USULAN BACKLOG]

### Label: Frontend, Multimedia, Accessibility

### Description:

```
Assignee: Zahwa Rahmadhania
Description (DoD):
Teknis: Opsi multi-bahasa (Bahasa Indonesia, Daerah Minang, dan Inggris) pada pengumuman suara panggilan monitor TV.
Bukti Fisik: Dropdown pilihan bahasa di panel pengaturan sistem monitor display.
Bebas Bug: Audio pemanggilan berjalan sinkron sesuai pilihan bahasa yang diklik tanpa bentrok audio.
```

### Checklist Task

1. Language Settings & Audio Module

    [ ] Tambahkan opsi pilihan bahasa panggilan di panel konfigurasi sistem Super Admin.
    [ ] Buat modul suara bel alternatif dalam bahasa Minang dan Inggris pada halaman display monitor.
