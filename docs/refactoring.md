# Dokumentasi Refactoring Kode

Dokumen ini menjelaskan teknik optimasi dan pemeliharaan kode (code maintenance) yang diterapkan pada **Sistem Manajemen Antrean MPP Kota Sawahlunto (Spektra)**. Proses refactoring difokuskan untuk meningkatkan fleksibilitas evolusi perangkat lunak (software evolution) dan memudahkan kolaborasi tim pengembang SPEKTRA.

---

## 🛑 1. Sebelum Refactoring (Problem)

Sebelum dilakukannya restrukturisasi kode, sistem memiliki beberapa kendala arsitektur (anti-patterns) yang menghambat skalabilitas:

### A. God-Controllers (Controller Gemuk)
Sebagian besar logika bisnis, termasuk validasi data input, manajemen transaksi database, pemrosesan nomor antrean, hingga penyiaran event WebSocket ditulis langsung di dalam satu metode Controller (misalnya pada `CheckInController` dan `CounterController`).
- **Dampak:** Controller menjadi sangat panjang (ratusan baris kode), sulit dibaca, tidak dapat digunakan kembali (*not reusable*), dan sangat sulit untuk dilakukan pengujian unit (*unit testing*).

### B. Direct Database Queries (Query SQL Langsung)
Penulisan kueri database yang kompleks menggunakan DB facade raw query atau pemanggilan rantai Eloquent ORM yang panjang ditulis langsung di dalam Controller tanpa enkapsulasi.
- **Dampak:** Perubahan skema database sekecil apa pun memaksa developer mencari dan mengubah query di seluruh controller satu per satu.

### C. Fat Routing Files (Berkas Rute Menumpuk)
Semua rute aplikasi untuk 4 peran pengguna yang berbeda ditulis menumpuk di dalam satu file `routes/web.php` secara linier.
- **Dampak:** File rute menjadi sangat panjang, sulit dicari, dan rentan menimbulkan konflik git (*merge conflict*) ketika beberapa developer mengedit file rute secara bersamaan.

### D. Cluttered Single-Blade Views (Tampilan Blade Raksasa)
Halaman dashboard seperti halaman operator gerai atau halaman antrean admin dirancang dalam satu file Blade tunggal berukuran ribuan baris, yang mencampurkan markup HTML, kelas utility Tailwind CSS, script Alpine.js, dan modal dialog.
- **Dampak:** Sangat melelahkan untuk melacak penutup tag HTML atau mengubah tata letak kartu tertentu tanpa merusak bagian halaman yang lain.

---

## 🛠️ 2. Perubahan yang Diterapkan (Solution)

Restrukturisasi kode dilakukan dengan membagi tanggung jawab komponen secara modular mengikuti prinsip SOLID:

### A. Penerapan Service Layer (Lapisan Layanan Mandiri)
Logika bisnis inti yang bersifat transaksional diekstrak dari Controller dan dipindahkan ke dalam kelas Layanan khusus (*Service Classes*) di bawah direktori `app/Services/`.
- **Contoh Kasus**: Pembuatan tiket walk-in dan kalkulasi nomor urut antrean dipindahkan ke `app/Services/WalkInTicketService.php`. Serta pengolahan status pemanggilan antrean diekstrak ke `app/Services/QueueMonitorService.php`.
- **Implementasi**:
  ```php
  // app/Http/Controllers/WalkInTicketController.php
  public function store(StoreWalkInTicketRequest $request, WalkInTicketService $service)
  {
      $queue = $service->createTicket($request->validated());
      return back()->with('success', "Tiket {$queue->queue_number} berhasil dicetak.");
  }
  ```

### B. Penggunaan Eloquent Query Scopes & Enkapsulasi Model
Kueri pencarian database yang spesifik didefinisikan ke dalam metode Model sebagai Local Scopes atau enkapsulasi logika model untuk menyembunyikan detail kueri dari Controller.
- **Contoh Kasus**: Memeriksa apakah tiket booking online masih bisa diproses check-in dibungkus ke dalam model `Booking`.
- **Implementasi**:
  ```php
  // app/Models/Booking.php
  public function canBeCheckedIn(): bool
  {
      return $this->status === 'Pending' && $this->booking_date->isToday();
  }
  ```

### C. Segmentasi Berkas Rute (Domain-Specific Routes)
Pemisahan rute web utama menjadi beberapa berkas rute terpisah berdasarkan domain peran dan hak akses pengguna. Rute-rute ini kemudian didaftarkan secara otomatis melalui Service Provider.
- Rute dipisah menjadi:
  - `routes/web.php` (Rute publik & autentikasi)
  - `routes/admin.php` (Rute Super Admin)
  - `routes/fo.php` (Rute Front Office)
  - `routes/gerai.php` (Rute Operator Loket/Gerai)

### D. Dekomposisi Halaman Blade (Reusable & Anonymous Components)
File Blade raksasa dipecah menjadi komponen-komponen kecil yang dapat digunakan kembali menggunakan fitur `@include` atau *Anonymous Blade Components* Laravel.
- **Contoh Kasus**: Panel gerai pada Super Admin dipecah ke dalam folder `resources/views/super_admin/gerai/components/`:
  - `metrics.blade.php` (Kartu ringkasan jumlah antrean)
  - `table.blade.php` (Tabel daftar antrean gerai)
  - `modal.blade.php` (Form pop-up tambah/edit gerai)
  - `cards.blade.php` (Kartu visual individu)

---

## 📈 3. Alasan dan Dampak Refactoring

Langkah refactoring ini memberikan dampak positif yang signifikan pada kualitas perangkat lunak:

| Aspek | Dampak Sebelum Refactoring | Dampak Setelah Refactoring |
|---|---|---|
| **Maintainability Index** | Rendah. Memperbaiki satu bug berisiko merusak fitur lain karena kode saling tumpang tindih. | Tinggi. Logika bisnis terisolasi di kelas Service sehingga bug dapat diisolasi dan diperbaiki dengan cepat tanpa efek samping. |
| **Kemudahan Onboarding Developer** | Developer baru kesulitan memahami alur program karena file yang terlalu panjang dan kompleks. | Mudah. Kode terdistribusi secara intuitif sesuai dengan konvensi standar Laravel dan dokumentasi arsitektur. |
| **Keamanan Transaksi (Atomicity)** | Rentan terjadi inkonsistensi data jika server terputus di tengah pemrosesan query bertingkat. | Terjamin. Logika transaksional dibungkus dalam `DB::transaction()` di dalam Service, memastikan semua kueri sukses bersamaan atau dibatalkan sepenuhnya. |
| **Keterbacaan Kode (Clean Code)** | File Controller berkisar 400 - 600 baris. | Controller sangat ramping, rata-rata kurang dari 80 baris, hanya bertugas menerima request dan mengembalikan response. |
| **Kolaborasi Tim (Git Flow)** | Sering terjadi konflik penggabungan kode (*merge conflict*) karena semua orang mengedit `web.php` dan file view yang sama. | Konflik git berkurang drastis karena developer bekerja pada file modul terpisah (misalnya rute `fo.php` vs `gerai.php`). |
