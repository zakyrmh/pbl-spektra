# Spesifikasi Fitur Aplikasi

Dokumen ini mendokumentasikan pemetaan fitur utama aplikasi **PBL Spektra — Sistem Manajemen Antrean MPP Kota Sawahlunto** yang diturunkan langsung dari analisis Use Case Diagram, Activity Diagram, dan Sequence Diagram sistem.

---

## 🔑 1. Autentikasi dan Registrasi Pengguna

### Nama Fitur

Autentikasi dan Registrasi Pengguna

### Tujuan Fitur

Menyediakan mekanisme pendaftaran akun mandiri bagi pengunjung (warga) serta gerbang masuk yang aman bagi seluruh aktor sistem (Super Admin, Admin Front Office, Petugas Gerai, dan Pengunjung) dengan menerapkan pembatasan hak akses berbasis peran (Role-Based Access Control / RBAC) demi keamanan data.

### Aktor

- Pengunjung / Warga (`customer`)
- Admin Front Office (`front_office`)
- Petugas Loket/Gerai (`gerai`)
- Super Admin (`super_admin`)

### Alur Fitur

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

### Route / Controller Terkait

- `GET /register` | `AuthController@register` (Menampilkan form registrasi)
- `POST /register` | `AuthController@store` (Menyimpan data registrasi)
- `GET /login` | `AuthController@login` (Menampilkan form login)
- `POST /login` | `AuthController@authenticate` (Memproses login)
- `POST /logout` | `AuthController@logout` (Mengakhiri sesi pengguna)
- `GET /forgot-password` | `AuthController@forgotPassword` (Menampilkan form lupa password)
- `POST /forgot-password` | `AuthController@sendResetLink` (Mengirim email reset)

### Screenshot Fitur

![Halaman Login](screenshots/login.png)
_Gambar 1: Halaman masuk terpusat untuk seluruh aktor sistem._

---

## 📅 2. Booking Mandiri (Reservasi Online)

### Nama Fitur

Booking Mandiri (Reservasi Online)

### Tujuan Fitur

Memungkinkan pengunjung untuk memesan slot pelayanan instansi secara online sebelum mendatangi kantor MPP Kota Sawahlunto. Fitur ini bertujuan untuk menyebarkan beban kedatangan warga secara merata sepanjang hari dan mengeliminasi waktu tunggu di lokasi fisik.

### Aktor

- Pengunjung / Warga (`customer`)

### Alur Fitur

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

### Route / Controller Terkait

- `GET /customer/booking` | `BookingController@index` (Menampilkan daftar booking pengguna)
- `GET /customer/booking/create` | `BookingController@create` (Menampilkan formulir reservasi)
- `POST /customer/booking` | `BookingController@store` (Memproses penyimpanan reservasi baru)

### Screenshot Fitur

![Pemesanan Antrean Mandiri](screenshots/booking.png)
_Gambar 2: Formulir pemesanan slot antrean online oleh pengunjung._

---

## 🔍 3. Verifikasi Lapangan & Penerbitan Antrean (Front Office Check-In)

### Nama Fitur

Verifikasi Lapangan & Penerbitan Antrean (Front Office Check-In)

### Tujuan Fitur

Membantu petugas Front Office (FO) di pintu masuk MPP Sawahlunto untuk memvalidasi tiket online pengunjung saat mereka tiba di lokasi fisik, serta mendaftarkan pengunjung walk-in secara langsung. Fitur ini berfungsi sebagai gerbang tunggal penerbitan nomor antrean aktif.

### Aktor

- Admin Front Office (`front_office`)

### Alur Fitur

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

### Route / Controller Terkait

- `GET /fo/check-in` | `Admin\FO\CheckInController@index` (Menampilkan halaman pencarian & check-in)
- `POST /fo/check-in/verify` | `Admin\FO\CheckInController@verify` (Memproses pencarian & pengisian NIK)
- `POST /api/fo/bookings/{booking}/checkin` | `Admin\FO\CheckInController@checkInApi` (Endpoint API transaksi verifikasi & cetak)
- `POST /api/fo/queues/walkin` | `Admin\FO\CheckInController@walkInApi` (Endpoint API pendaftaran walk-in)

### Screenshot Fitur

![Verifikasi Front Office](screenshots/front-office.png)
_Gambar 3: Halaman kerja verifikator Front Office._

---

## 📢 4. Core Pemanggilan Antrean (Papan Kontrol Gerai)

### Nama Fitur

Core Pemanggilan Antrean (Papan Kontrol Gerai)

### Tujuan Fitur

Menjadi pusat kendali bagi petugas gerai pelayanan dalam memanggil, memproses, dan menyelesaikan antrean pengunjung yang berada di ruang tunggu. Fitur ini dirancang untuk mendistribusikan antrean secara real-time dan berurutan.

### Aktor

- Petugas Loket/Gerai (`gerai`)

### Alur Fitur

1. Petugas gerai login dan masuk ke dashboard loket miliknya. Halaman ini hanya menampilkan daftar antrean berstatus `Waiting` yang ditujukan untuk gerai instansinya sendiri (**RBAC Ketat**).
2. **Proses Pemanggilan (`Call`)**:
    - Petugas mengeklik tombol **"Panggil Selanjutnya"** (atau ikon pengeras suara).
    - Sistem mengubah status antrean di tabel `queues` menjadi `Serving` (Sedang Dilayani) dan mencatatkan waktu pemanggilan di kolom `called_at`.
    - Sistem memicu event broadcast `QueueCalled` via WebSocket (Pusher) dengan membawa payload `queue_number` dan `counter_name`.
    - Layar monitor display publik (`/display`) di ruang tunggu secara instan menerima data pemanggilan tersebut, memutar suara bel panggilan ("Ting-Tung"), dilanjutkan dengan suara text-to-speech otomatis berbahasa Indonesia: _"Nomor antrean BNR-002, silakan menuju Loket Bank Nagari"_.
    - Nomor antrean yang dipanggil akan berkedip/berdenyut (_pulse_) di layar monitor untuk menarik perhatian pengunjung.
3. **Proses Penyelesaian (`Complete`)**:
    - Setelah pelayanan selesai dilakukan, petugas gerai mengeklik tombol **"Selesai Dilayani"**.
    - Sistem mengubah status antrean di tabel `queues` menjadi `Completed` dan mencatatkan waktu selesai di kolom `completed_at`.
    - Sistem mengirimkan instruksi ke server untuk mengizinkan pengunjung mengisi modul feedback kepuasan.
4. **Proses Pengabaian/Terlewat (`Skip`)**:
    - Jika pengunjung tidak kunjung datang ke loket setelah dipanggil berulang kali, petugas dapat mengeklik tombol **"Lewati Antrean"**.
    - Sistem mengubah status antrean menjadi `Skipped`. Nomor antrean tersebut dapat dipanggil ulang di akhir hari jika diperlukan.

### Route / Controller Terkait

- `GET /gerai/dashboard` | `Admin\Gerai\CounterController@dashboard` (Tampilan utama daftar antrean operator)
- `POST /gerai/queues/{queue}/call` | `Admin\CounterController@callQueue` (Aksi pemanggilan nomor)
- `POST /gerai/queues/{queue}/finish` | `Admin\CounterController@finishService` (Aksi penyelesaian pelayanan)
- `POST /gerai/queues/{queue}/skip` | `Admin\CounterController@skipQueue` (Aksi melewati nomor antrean)

### Screenshot Fitur

![Papan Panggil Gerai](screenshots/gerai-caller.png)
_Gambar 4: Halaman operasional pemanggilan antrean oleh operator gerai._

---

## 📺 5. Layar Monitor Display Antrean (Display Ruang Tunggu)

### Nama Fitur

Layar Monitor Display Antrean (Display Ruang Tunggu)

### Tujuan Fitur

Menampilkan status antrean aktif per loket secara real-time di ruang tunggu MPP Kota Sawahlunto. Fitur ini memandu pengunjung dengan sinyal visual dan audio bel serta pemanggilan otomatis berbasis suara (_text-to-speech_) tanpa interaksi manual dari warga.

### Aktor

- Pengunjung / Publik (sebagai pemantau)

### Alur Fitur

1. Petugas IT/FO membuka halaman `/display` pada layar televisi monitor ruang tunggu MPP.
2. Sistem memuat daftar seluruh loket aktif beserta nomor antrean terakhir yang sedang dilayani dari basis data.
3. Sistem mendengarkan event pemanggilan (`QueueCalled`) yang dikirimkan melalui server WebSocket (Pusher).
4. Ketika event diterima, sistem melakukan pembaruan layout secara instan:
    - Menampilkan nomor antrean yang dipanggil secara berkedip (_pulsating animation_) pada bagian display utama.
    - Memutar audio lonceng/bel panggilan ("Ting-Tung").
    - Memanggil fungsi Web Speech API (_text-to-speech_) browser untuk membacakan panggilan secara otomatis (contoh: _"Nomor antrean DPTK-005, silakan menuju Loket Dinas Ketenagakerjaan"_).

### Route / Controller Terkait

- `GET /display` | `DisplayController@index` (Menampilkan halaman display antrean publik)
- `GET /api/display/queues` | `DisplayController@getQueuesApi` (Mengambil data status antrean gerai terkini)

### Screenshot Fitur

![Layar Monitor Display](screenshots/display.png)
_Gambar 5: Tampilan layar monitor informasi antrean real-time di ruang tunggu._

---

## ✍️ 6. Modul Umpan Balik Kepuasan Pengunjung (Feedback & Rating)

### Nama Fitur

Modul Umpan Balik Kepuasan Pengunjung (Feedback & Rating)

### Tujuan Fitur

Mengumpulkan penilaian, ulasan, dan tingkat kepuasan pengunjung setelah selesai menerima pelayanan dari salah satu gerai instansi di MPP Sawahlunto guna menjaga kualitas dan transparansi pelayanan publik.

### Aktor

- Pengunjung / Warga (`customer`)

### Alur Fitur

1. Setelah status pelayanan antrean diubah menjadi `Completed` oleh petugas gerai, sistem mencatat kelayakan pengisian feedback untuk sesi kunjungan tersebut.
2. Pengunjung menerima link kuesioner atau mengakses menu "Umpan Balik Layanan" melalui dasbor mereka di `/customer/feedback/{queue_id}`.
3. Pengunjung mengisi form feedback berupa: rating bintang (1 s.d 5), pilihan indikator kepuasan (keramahan, kecepatan, kejelasan informasi), serta saran/komentar tertulis.
4. Sistem memvalidasi bahwa antrean tersebut memang berstatus selesai dan belum pernah diisi ulasannya (menghindari duplikasi pengisian feedback).
5. Sistem menyimpan ulasan ke tabel `feedbacks` dan menghitung akumulasi kepuasan gerai terkait untuk dashboard analisis kinerja.

### Route / Controller Terkait

- `GET /customer/feedback/{queue}` | `Customer\FeedbackController@create` (Menampilkan formulir umpan balik kepuasan)
- `POST /customer/feedback/{queue}` | `Customer\FeedbackController@store` (Menyimpan data umpan balik ke database)

### Screenshot Fitur

![Formulir Umpan Balik](screenshots/feedback.png)
_Gambar 6: Tampilan form survei kepuasan pelayanan gerai bagi warga._

---

## 📊 7. Laporan dan Dashboard Analitik (Super Admin Reports)

### Nama Fitur

Laporan dan Dashboard Analitik (Super Admin Reports)

### Tujuan Fitur

Menyediakan ringkasan eksekutif dan analisis data performa antrean serta kepuasan gerai di MPP Kota Sawahlunto bagi Super Admin dalam mengawasi efektivitas pelayanan secara keseluruhan.

### Aktor

- Super Admin (`super_admin`)

### Alur Fitur

1. Super Admin mengakses menu "Laporan & Statistik" pada dasbor administrasi.
2. Sistem merender berbagai widget interaktif:
    - Tren jumlah kunjungan/antrean harian/bulanan (Line Chart).
    - Distribusi antrean per instansi gerai (Pie/Bar Chart).
    - Rata-rata waktu tunggu (_waiting time_) dan waktu pelayanan (_serving time_).
    - Rata-rata nilai indeks kepuasan masyarakat (IKM) gerai.
3. Super Admin dapat memfilter visualisasi berdasarkan rentang waktu tertentu atau instansi tertentu.
4. Super Admin dapat mengklik tombol "Ekspor Laporan" untuk mengunduh kompilasi data tersebut ke dalam format PDF terformat atau spreadsheet Excel (`.xlsx`/`.csv`).

### Route / Controller Terkait

- `GET /admin/reports` | `Admin\ReportController@index` (Menampilkan dashboard analisis utama)
- `GET /admin/reports/export` | `Admin\ReportController@export` (Memproses pembuatan file ekspor PDF/Excel)

### Screenshot Fitur

![Laporan Analitik](screenshots/reports.png)
_Gambar 7: Visualisasi grafik statistik kunjungan dan penilaian gerai._
