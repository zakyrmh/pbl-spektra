# Identifikasi Dependency / Package Laravel

**Mata Kuliah:** Konstruksi dan Evolusi Perangkat Lunak  
**Proyek:** PBL Spektra — Sistem Manajemen Antrean MPP Sawahlunto  
**Sumber Konfigurasi:** `composer.json`

---

## 1. laravel/framework

### What
Laravel Framework adalah kerangka kerja (framework) inti PHP yang menjadi fondasi utama seluruh aplikasi. Package ini menyediakan seluruh fitur dasar Laravel seperti routing, Eloquent ORM, middleware, Blade templating, dependency injection, dan sistem event.

### Why
Package ini diperlukan karena menjadi tulang punggung dari seluruh proyek. Tanpa `laravel/framework`, tidak ada satu pun fitur aplikasi yang dapat berjalan — mulai dari routing URL, koneksi database, hingga autentikasi pengguna.

### Who
- **Developer** — Menggunakan seluruh fitur framework untuk membangun fitur aplikasi.
- **Seluruh pengguna sistem** — Secara tidak langsung merasakan dampaknya karena semua interaksi sistem diproses oleh framework ini.

### When
Digunakan sepanjang siklus hidup aplikasi, mulai dari menerima HTTP request dari browser pengguna, memproses logika bisnis, hingga mengembalikan response ke client.

### Where
Digunakan di seluruh lapisan aplikasi: routes, controllers, models, middleware, views, dan services.

### How
Diinstal melalui Composer dengan menjalankan `composer install`. Framework akan otomatis terkonfigurasi melalui mekanisme service provider dan auto-discovery yang tersedia di `config/app.php`.

**Referensi:** https://packagist.org/packages/laravel/framework

---

## 2. laravel/tinker

### What
Laravel Tinker adalah REPL (Read-Eval-Print Loop) interaktif untuk Laravel yang memungkinkan developer menjalankan kode PHP secara langsung di dalam konteks aplikasi melalui command line.

### Why
Package ini diperlukan untuk mempercepat proses debugging dan pengujian logika aplikasi tanpa harus membuat halaman atau endpoint khusus. Developer bisa langsung berinteraksi dengan model database, menguji query Eloquent, atau memanggil fungsi tertentu secara instan.

### Who
- **Developer** — Merupakan satu-satunya pengguna langsung tool ini selama proses pengembangan.

### When
Digunakan selama fase pengembangan dan debugging, misalnya saat developer perlu mengecek data di database, menguji relasi model, atau mensimulasikan proses bisnis secara manual.

### Where
Digunakan di lingkungan pengembangan (development environment) melalui terminal dengan perintah `php artisan tinker`.

### How
Diinstal melalui Composer dan tersedia sebagai perintah Artisan. Developer cukup menjalankan `php artisan tinker` di terminal, lalu dapat mengetikkan ekspresi PHP atau Eloquent query secara interaktif, misalnya `App\Models\User::all()`.

**Referensi:** https://packagist.org/packages/laravel/tinker

---

## 3. laravel/boost

### What
Laravel Boost adalah package resmi dari tim Laravel yang menyediakan generator dan utilitas untuk mempercepat scaffolding dan pembangunan komponen aplikasi modern, termasuk integrasi yang lebih erat dengan tooling front-end.

### Why
Package ini diperlukan untuk mempercepat proses setup awal dan pengembangan iteratif, sehingga developer dapat fokus pada logika bisnis alih-alih konfigurasi boilerplate yang berulang.

### Who
- **Developer** — Pengguna utama yang memanfaatkan generator dan perintah Artisan tambahan yang disediakan package ini.

### When
Digunakan selama fase awal pembangunan proyek (project scaffolding) dan sepanjang fase pengembangan ketika membuat komponen atau modul baru.

### Where
Digunakan di lingkungan pengembangan, tersedia melalui perintah Artisan di terminal. Tidak diperlukan di lingkungan produksi.

### How
Diinstal sebagai dependency development melalui Composer (`require-dev`). Setelah terinstal, perintah tambahan Artisan dari Boost tersedia untuk digunakan. Update dapat dilakukan melalui `@php artisan boost:update`.

**Referensi:** https://packagist.org/packages/laravel/boost

---

## 4. laravel/pail

### What
Laravel Pail adalah tool CLI untuk melakukan log tailing secara real-time langsung di terminal, menampilkan log aplikasi Laravel dengan tampilan yang bersih dan berwarna.

### Why
Package ini diperlukan agar developer dapat memantau log aplikasi secara langsung dan interaktif selama pengembangan, sehingga proses debugging menjadi lebih cepat dibandingkan harus membuka file `storage/logs/laravel.log` secara manual.

### Who
- **Developer** — Menggunakan tool ini untuk memantau aktivitas aplikasi secara real-time selama pengembangan.

### When
Digunakan selama fase pengembangan, terutama saat menguji alur fitur seperti proses pemanggilan antrean, pengiriman notifikasi, atau proses autentikasi yang menghasilkan log.

### Where
Digunakan di lingkungan pengembangan melalui terminal dengan perintah `php artisan pail`.

### How
Diinstal sebagai dependency development melalui Composer. Setelah aktif, Pail akan menampilkan output log secara streaming langsung di terminal. Dapat dijalankan bersamaan dengan server lokal melalui skrip `composer dev`.

**Referensi:** https://packagist.org/packages/laravel/pail

---

## 5. laravel/pint

### What
Laravel Pint adalah code formatter PHP yang mengikuti standar penulisan kode resmi Laravel, dibangun di atas PHP-CS-Fixer.

### Why
Package ini diperlukan untuk menjaga konsistensi gaya penulisan kode (code style) di seluruh proyek secara otomatis, sehingga semua anggota tim menulis kode dengan format yang seragam tanpa perlu merapikan manual.

### Who
- **Developer / Tim Pengembang** — Seluruh anggota tim yang berkontribusi pada kode sumber proyek.

### When
Digunakan sebelum melakukan commit ke repository atau secara berkala selama pengembangan untuk memastikan kode tetap konsisten dan rapi.

### Where
Digunakan di lingkungan pengembangan melalui terminal. Dapat diintegrasikan ke dalam alur CI/CD untuk memeriksa format kode secara otomatis.

### How
Diinstal sebagai dependency development melalui Composer. Dijalankan menggunakan perintah `./vendor/bin/pint` di root proyek untuk memformat seluruh file PHP secara otomatis.

**Referensi:** https://packagist.org/packages/laravel/pint

---

## 6. pusher/pusher-php-server

### What
Pusher PHP Server adalah library resmi dari Pusher untuk mengintegrasikan layanan WebSocket real-time ke dalam aplikasi PHP/Laravel, memungkinkan server untuk mempublikasikan event ke client secara langsung.

### Why
Package ini diperlukan agar sistem antrean dapat memperbarui tampilan nomor antrean di layar monitor secara otomatis tanpa perlu me-refresh halaman. Hal ini sangat krusial untuk pengalaman pengguna di ruang tunggu MPP Sawahlunto.

### Who
- **Petugas Loket** — Secara tidak langsung memicu event saat menekan tombol "Panggil Antrean".
- **Pengunjung / Pemohon** — Melihat perubahan nomor antrean secara real-time di layar monitor.
- **Developer** — Mengkonfigurasi channel dan event broadcasting.

### When
Digunakan setiap kali petugas loket menekan tombol panggil antrean, sistem akan mengirimkan event melalui Pusher ke semua client yang terhubung (layar monitor antrean).

### Where
Diimplementasikan pada layer **backend** di dalam Laravel Event dan Listener, serta dikonsumsi di halaman **layar monitor antrean** (front-end) menggunakan Pusher JS client.

### How
Diinstal melalui Composer, lalu dikonfigurasi di file `.env` dengan kredensial Pusher (App ID, Key, Secret, Cluster). Laravel Broadcasting kemudian dikonfigurasi untuk menggunakan driver Pusher. Event dibuat menggunakan `php artisan make:event` dan di-broadcast menggunakan interface `ShouldBroadcast`.

**Referensi:** https://packagist.org/packages/pusher/pusher-php-server

---

## 7. maatwebsite/excel (Laravel Excel)

### What
Laravel Excel adalah package yang menyediakan kemampuan ekspor dan impor data dalam format Excel (`.xlsx`) dan CSV pada aplikasi Laravel, dengan API yang elegan dan mudah diintegrasikan.

### Why
Package ini diperlukan untuk memudahkan admin dalam menghasilkan laporan rekap data antrean dalam format Excel, tanpa harus membangun fungsi parsing dan penulisan file Excel dari nol menggunakan library level rendah seperti PhpSpreadsheet.

### Who
- **Admin Sistem** — Menggunakan fitur ekspor untuk mengunduh laporan statistik antrean.
- **Developer** — Membuat class Export yang mendefinisikan struktur laporan.

### When
Digunakan ketika admin membuka modul laporan dan menekan tombol "Ekspor ke Excel" untuk mengunduh data antrean dalam rentang waktu tertentu (harian, mingguan, atau bulanan).

### Where
Diimplementasikan pada **Modul Laporan** di panel Admin, khususnya pada controller laporan dan class Export yang didedikasikan untuk fitur ini.

### How
Diinstal melalui Composer (`composer require maatwebsite/excel`), kemudian konfigurasinya dipublikasikan dengan `php artisan vendor:publish`. Developer membuat class yang mengimplementasikan interface `FromCollection` atau `FromQuery`, lalu memanggil `Excel::download(new LaporanExport(), 'laporan.xlsx')` di dalam controller.

**Referensi:** https://packagist.org/packages/maatwebsite/excel

---

## 8. spatie/laravel-permission

### What
Spatie Laravel Permission adalah package untuk mengelola sistem Role (peran) dan Permission (hak akses) pengguna pada aplikasi Laravel, dengan dukungan penyimpanan di database dan integrasi langsung dengan model User.

### Why
Package ini diperlukan untuk membedakan hak akses secara ketat antara berbagai jenis pengguna dalam sistem (Admin, Petugas Loket, dan Pengunjung), sehingga setiap aktor hanya dapat mengakses fitur yang sesuai dengan perannya.

### Who
- **Admin Sistem** — Mendapat akses penuh ke seluruh modul termasuk laporan dan manajemen pengguna.
- **Petugas Loket** — Hanya dapat mengakses fitur pemanggilan dan pengelolaan antrean.
- **Pengunjung / Pemohon** — Hanya dapat melihat status antrean mereka sendiri.
- **Developer** — Mengkonfigurasi role dan permission sesuai kebutuhan sistem.

### When
Digunakan sepanjang siklus hidup aplikasi, terutama saat proses autentikasi dan setiap kali pengguna mencoba mengakses sebuah route atau fitur yang memerlukan verifikasi hak akses.

### Where
Diimplementasikan pada **seluruh modul sistem**: middleware route, controller, policy, dan Blade view (menggunakan direktif `@can` atau `@role` untuk menampilkan/menyembunyikan elemen UI).

### How
Diinstal melalui Composer, lalu migrasi database dijalankan untuk membuat tabel `roles`, `permissions`, dan pivot table terkait. Role dan permission didefinisikan melalui seeder atau panel admin. Proteksi route dilakukan menggunakan middleware `role:admin` atau `permission:kelola-antrean` langsung di file route.

**Referensi:** https://packagist.org/packages/spatie/laravel-permission

---

## Sumber Referensi

| No | Package | Sumber Referensi |
|----|---------|-----------------|
| 1 | laravel/framework | https://packagist.org/packages/laravel/framework |
| 2 | laravel/tinker | https://packagist.org/packages/laravel/tinker |
| 3 | laravel/boost | https://packagist.org/packages/laravel/boost |
| 4 | laravel/pail | https://packagist.org/packages/laravel/pail |
| 5 | laravel/pint | https://packagist.org/packages/laravel/pint |
| 6 | pusher/pusher-php-server | https://packagist.org/packages/pusher/pusher-php-server |
| 7 | maatwebsite/excel | https://packagist.org/packages/maatwebsite/excel |
| 8 | spatie/laravel-permission | https://packagist.org/packages/spatie/laravel-permission |
