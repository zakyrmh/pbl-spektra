# Spesifikasi Fitur Aplikasi

Dokumen ini mendokumentasikan pemetaan fitur utama aplikasi **PBL Spektra — Sistem Manajemen Antrean MPP Kota Sawahlunto** yang diturunkan langsung dari analisis Use Case Diagram, Activity Diagram, dan Sequence Diagram sistem.

---

## 🔑 1. Autentikasi dan Registrasi Pengguna

### Objektif
Menyediakan mekanisme pendaftaran akun mandiri bagi pengunjung (warga) serta gerbang masuk yang aman bagi seluruh aktor sistem (Super Admin, Admin Front Office, Petugas Gerai, dan Pengunjung) dengan menerapkan pembatasan hak akses berbasis peran (Role-Based Access Control / RBAC) demi keamanan data.

### Aktor
- Pengunjung / Warga (`customer`)
- Admin Front Office (`front_office`)
- Petugas Loket/Gerai (`gerai`)
- Super Admin (`super_admin`)

### Alur Fungsional
1. **Registrasi Mandiri (Pengunjung)**:
   - Pengunjung mengakses halaman `/register`.
   - Pengunjung mengisi formulir pendaftaran yang meliputi: Nama Lengkap, NIK (16 digit), Email, Nomor Telepon, dan Kata Sandi.
   - Sistem memvalidasi keunikan Email dan NIK di tabel `users` untuk mencegah duplikasi identitas.
   - Jika validasi lolos, sistem menyimpan akun baru dengan kata sandi yang di-hash menggunakan algoritma Bcrypt.
2. **Login Sistem (Semua Role)**:
   - Pengguna mengakses halaman `/login` dan memasukkan Email serta Kata Sandi.
   - Sistem memverifikasi kredensial. Jika salah, sistem mengembalikan umpan balik kesalahan.
   - Jika kredensial cocok, sistem membuat sesi login aktif dan membaca peran (`role`) pengguna.
   - Pengguna diarahkan secara otomatis ke halaman dashboard masing-masing berdasarkan hak akses perannya.
3. **Reset Kata Sandi (Semua Role)**:
   - Pengguna yang lupa kata sandi mengakses `/forgot-password`, memasukkan alamat email terdaftar.
   - Sistem memvalidasi email, membuat token reset password unik (berlaku 60 menit) di tabel `password_reset_tokens`, dan mengirimkan link reset ke email pengguna.
   - Pengguna mengeklik tautan tersebut, mengisi kata sandi baru di halaman `/reset-password/{token}`, dan sistem memperbarui data kata sandi pengguna.

### Rute & Controller Terkait
- `GET /register` | `AuthController@register` (Menampilkan form registrasi)
- `POST /register` | `AuthController@store` (Menyimpan data registrasi)
- `GET /login` | `AuthController@login` (Menampilkan form login)
- `POST /login` | `AuthController@authenticate` (Memproses login)
- `POST /logout` | `AuthController@logout` (Mengakhiri sesi pengguna)
- `GET /forgot-password` | `AuthController@forgotPassword` (Menampilkan form lupa password)
- `POST /forgot-password` | `AuthController@sendResetLink` (Mengirim email reset)

### Pratinjau Antarmuka
![Halaman Login](docs/screenshots/login.png)
*Gambar 1: Halaman masuk terpusat untuk seluruh aktor sistem.*

---

## 📅 2. Booking Mandiri (Reservasi Online)

### Objektif
Memungkinkan pengunjung untuk memesan slot pelayanan instansi secara online sebelum mendatangi kantor MPP Kota Sawahlunto. Fitur ini bertujuan untuk menyebarkan beban kedatangan warga secara merata sepanjang hari dan mengeliminasi waktu tunggu di lokasi fisik.

### Aktor
- Pengunjung / Warga (`customer`)

### Alur Fungsional
1. Pengunjung masuk ke akunnya dan menavigasi ke menu "Booking Antrean Mandiri".
2. Pengunjung memilih tanggal rencana kunjungan, instansi gerai yang dituju, dan jenis layanan spesifik.
3. Sistem secara dinamis memvalidasi ketersediaan jadwal layanan berdasarkan tabel `schedules`:
   - Mengecek apakah kuota pada tanggal tersebut masih tersedia (`quota_used < quota_total`).
   - Memastikan status jadwal dalam kondisi terbuka (`is_open = true`).
4. Sistem memvalidasi aturan bisnis **BR-06**: Memastikan NIK pengunjung tidak memiliki booking aktif berstatus `Pending` untuk layanan yang sama pada tanggal tersebut.
5. Jika semua aturan bisnis terpenuhi, sistem mengunci transaksi (database transaction):
   - Membuat rekor booking baru di tabel `bookings` dengan status awal `Pending`.
   - Menghasilkan kode booking unik berupa UUID v4 (`booking_code`).
   - Memperbarui jumlah penggunaan kuota (`quota_used` bertambah 1) pada tabel `schedules`.
6. Sistem memicu pengiriman notifikasi email yang berisi rincian tiket reservasi, persyaratan dokumen, dan lampiran QR Code.
7. Pengunjung diarahkan ke halaman "Reservasi Saya" yang merender tiket digital berisi QR Code dan instruksi kedatangan.

### Rute & Controller Terkait
- `GET /customer/booking` | `BookingController@index` (Menampilkan daftar booking pengguna)
- `GET /customer/booking/create` | `BookingController@create` (Menampilkan formulir reservasi)
- `POST /customer/booking` | `BookingController@store` (Memproses penyimpanan reservasi baru)

### Pratinjau Antarmuka
![Pemesanan Antrean Mandiri](docs/screenshots/booking.png)
*Gambar 2: Formulir pemesanan slot antrean online oleh pengunjung.*

---

## 🔍 3. Verifikasi Lapangan & Penerbitan Antrean (Front Office Check-In)

### Objektif
Membantu petugas Front Office (FO) di pintu masuk MPP Sawahlunto untuk memvalidasi tiket online pengunjung saat mereka tiba di lokasi fisik, serta mendaftarkan pengunjung walk-in secara langsung. Fitur ini berfungsi sebagai gerbang tunggal penerbitan nomor antrean aktif.

### Aktor
- Admin Front Office (`front_office`)

### Alur Fungsional
1. **Verifikasi Booking Online**:
   - Pengunjung datang ke area loket Front Office dan menunjukkan QR Code tiket online.
   - Petugas FO memindai QR Code menggunakan barcode scanner atau mengetikkan 8 karakter awal kode booking secara manual di kolom pencarian.
   - Sistem mencocokkan data di tabel `bookings` berstatus `Pending`.
   - **Pemeriksaan Kelengkapan Profil (NIK)**: Jika data NIK pengunjung di tabel `users` masih kosong, sistem secara dinamis memunculkan kolom input NIK (16 digit) wajib isi secara inline untuk dilengkapi oleh petugas FO sebelum melanjutkan.
   - Petugas mengeklik tombol "Approve & Check-in".
   - Di dalam transaksi basis data (`DB::transaction`):
     - Sistem mengubah status booking dari `Pending` menjadi `Checked-In` (atau `Confirmed`) dan menyimpan waktu check-in.
     - Menghitung nomor antrean urut hari itu untuk gerai terkait (`existingCount + 1`).
     - Membuat rekor antrean aktif di tabel `queues` dengan status `Waiting` dan nomor antrean berformat `[INISIAL_GERAI]-[NOMOR_URUT]` (contoh: `DPTK-005`).
   - Sistem memicu event broadcast `QueueCreated` via WebSocket (Pusher) untuk memicu pembaruan otomatis di dasbor petugas gerai terkait dan display monitor ruang tunggu.
   - Printer thermal mencetak tiket antrean fisik berisi nomor antrean, nama gerai, dan QR Code untuk diserahkan ke pengunjung.
2. **Pendaftaran Pengunjung Walk-In (Manual)**:
   - Pengunjung tanpa HP/akses online dilayani secara manual oleh petugas FO.
   - Petugas mengisi form walk-in: Nama Lengkap, NIK, No. Telepon, Instansi Gerai, dan Keperluan Kunjungan.
   - Sistem memvalidasi sisa kuota hari itu di `schedules`. Jika tersedia, sistem menyimpan data ke tabel `visitors` dan secara otomatis menerbitkan rekor antrean aktif di tabel `queues` berstatus `Waiting`.

### Rute & Controller Terkait
- `GET /fo/check-in` | `Admin\FO\CheckInController@index` (Menampilkan halaman pencarian & check-in)
- `POST /fo/check-in/verify` | `Admin\FO\CheckInController@verify` (Memproses pencarian & pengisian NIK)
- `POST /api/fo/bookings/{booking}/checkin` | `Admin\FO\CheckInController@checkInApi` (Endpoint API transaksi verifikasi & cetak)
- `POST /api/fo/queues/walkin` | `Admin\FO\CheckInController@walkInApi` (Endpoint API pendaftaran walk-in)

### Pratinjau Antarmuka
![Verifikasi Front Office](docs/screenshots/front-office.png)
*Gambar 3: Halaman kerja verifikator Front Office.*

---

## 📢 4. Core Pemanggilan Antrean (Papan Kontrol Gerai)

### Objektif
Menjadi pusat kendali bagi petugas gerai pelayanan dalam memanggil, memproses, dan menyelesaikan antrean pengunjung yang berada di ruang tunggu. Fitur ini dirancang untuk mendistribusikan antrean secara real-time dan berurutan.

### Aktor
- Petugas Loket/Gerai (`gerai`)

### Alur Fungsional
1. Petugas gerai login dan masuk ke dashboard loket miliknya. Halaman ini hanya menampilkan daftar antrean berstatus `Waiting` yang ditujukan untuk gerai instansinya sendiri (**RBAC Ketat**).
2. **Proses Pemanggilan (`Call`)**:
   - Petugas mengeklik tombol **"Panggil Selanjutnya"** (atau ikon pengeras suara).
   - Sistem mengubah status antrean di tabel `queues` menjadi `Serving` (Sedang Dilayani) dan mencatatkan waktu pemanggilan di kolom `called_at`.
   - Sistem memicu event broadcast `QueueCalled` via WebSocket (Pusher) dengan membawa payload `queue_number` dan `counter_name`.
   - Layar monitor display publik (`/display`) di ruang tunggu secara instan menerima data pemanggilan tersebut, memutar suara bel panggilan ("Ting-Tung"), dilanjutkan dengan suara text-to-speech otomatis berbahasa Indonesia: *"Nomor antrean BNR-002, silakan menuju Loket Bank Nagari"*.
   - Nomor antrean yang dipanggil akan berkedip/berdenyut (*pulse*) di layar monitor untuk menarik perhatian pengunjung.
3. **Proses Penyelesaian (`Complete`)**:
   - Setelah pelayanan selesai dilakukan, petugas gerai mengeklik tombol **"Selesai Dilayani"**.
   - Sistem mengubah status antrean di tabel `queues` menjadi `Completed` dan mencatatkan waktu selesai di kolom `completed_at`.
   - Sistem mengirimkan instruksi ke server untuk mengizinkan pengunjung mengisi modul feedback kepuasan.
4. **Proses Pengabaian/Terlewat (`Skip`)**:
   - Jika pengunjung tidak kunjung datang ke loket setelah dipanggil berulang kali, petugas dapat mengeklik tombol **"Lewati Antrean"**.
   - Sistem mengubah status antrean menjadi `Skipped`. Nomor antrean tersebut dapat dipanggil ulang di akhir hari jika diperlukan.

### Rute & Controller Terkait
- `GET /gerai/dashboard` | `Admin\Gerai\CounterController@dashboard` (Tampilan utama daftar antrean operator)
- `POST /gerai/queues/{queue}/call` | `Admin\Gerai\CounterController@callQueue` (Aksi pemanggilan nomor)
- `POST /gerai/queues/{queue}/finish` | `Admin\Gerai\CounterController@finishService` (Aksi penyelesaian pelayanan)
- `POST /gerai/queues/{queue}/skip` | `Admin\Gerai\CounterController@skipQueue` (Aksi melewati nomor antrean)

### Pratinjau Antarmuka
![Papan Panggil Gerai](docs/screenshots/gerai-caller.png)
*Gambar 4: Halaman operasional pemanggilan antrean oleh operator gerai.*
