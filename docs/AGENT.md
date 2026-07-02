# AGENT CONTEXT — Sistem Antrian Digital MPP Kota Sawahlunto
> SRS Mini v1.1 · Spektra TRPL 2C · Last updated: 27 Mar 2026
> Gunakan dokumen ini sebagai sumber kebenaran tunggal saat menulis kode, migrasi, seeder, atau test.

---

## 1. RINGKASAN SISTEM

Aplikasi web berbasis **Laravel 13 + MySQL 8 + TailwindCSS 4** untuk manajemen antrian digital terpusat di Mal Pelayanan Publik (MPP) Kota Sawahlunto. Menggantikan sistem antrian kertas manual.

**Arsitektur:** MVC · VPS Ubuntu 22.04 · Nginx · PHP 8.3+ · MySQL 8.0+

**Tidak ada dalam scope v1.1:** chatbot, analisis sentimen, integrasi kependudukan, mobile native app.

---

## 2. TECH STACK & DEPENDENCIES

| Layer | Teknologi |
|---|---|
| Backend framework | Laravel 13.x |
| Frontend/CSS | TailwindCSS 4.x |
| Database | MySQL 8.0+ |
| ORM | Laravel Eloquent |
| Auth | Laravel built-in (session, middleware `auth`) |
| Password reset | Laravel built-in (`password_reset_tokens` table) |
| Email | Laravel Mail + SMTP (Mailtrap dev / Mailgun prod) |
| PDF export | `barryvdh/laravel-dompdf` |
| Excel export | `maatwebsite/laravel-excel` |
| Real-time display | AJAX polling interval 5 detik |
| CSRF | Laravel CSRF token (semua form) |
| Password hashing | Bcrypt (default Laravel) |
| Scheduler | Laravel Scheduler via cron `* * * * * php artisan schedule:run` |

---

## 3. AKTOR & RBAC

| Role (string di DB) | Akses Utama | Batasan |
|---|---|---|
| `super_admin` | Semua modul + laporan + settings | — |
| `front_office` | Check-in, walk-in, batal booking, input umpan balik walk-in | Tidak bisa akses modul gerai |
| `gerai` | Panggil/selesai/skip antrian gerainya sendiri | Tidak bisa lihat antrian gerai lain (RBAC ketat) |
| `customer` | Reservasi online, lihat riwayat booking, umpan balik | Tidak bisa batalkan booking sendiri |

**Aturan kritis:**
- Petugas `gerai` A **tidak boleh** mengakses data antrian gerai B — enforced via Laravel Gates/Policies.
- Hanya `front_office` yang menerbitkan nomor antrian aktif (mengubah `Pending` → `Waiting`).
- Hanya `front_office` yang bisa membatalkan booking Customer.
- Halaman `/display` bisa diakses **tanpa login**.

---

## 4. DATABASE SCHEMA (12 Tabel)

### 4.1 Tabel Utama

```
users
  id, name, email (unique), nik (unique), phone, password (bcrypt),
  role ENUM('super_admin','front_office','gerai','customer'),
  counter_id (FK → counters, nullable, untuk role gerai),
  remember_token, timestamps, soft_deletes

departments
  id, name, inisial (unique, max 6 char), description, timestamps

services
  id, department_id (FK), name, description, timestamps

requirements
  id, service_id (FK), description, timestamps

counters
  id, department_id (FK), name, location, timestamps

schedules
  id, service_id (FK), date, quota_total, quota_used, is_open BOOL, timestamps

bookings
  id, user_id (FK → users, customer), service_id (FK), schedule_id (FK),
  booking_code UUID unique,
  status ENUM('Pending','Checked-In','Completed','Cancelled'),
  booking_date DATE, timestamps

visitors
  id, name, nik, phone, purpose (keperluan kunjungan), timestamps

queues
  id,
  booking_id (FK → bookings, nullable — null jika walk-in),
  visitor_id (FK → visitors, nullable — null jika online customer),
  counter_id (FK → counters),
  queue_number VARCHAR(12),   -- format: [INISIAL]-[001], contoh: TS-001
  status ENUM('Waiting','Serving','Completed','Skipped'),
  called_at TIMESTAMP nullable,
  completed_at TIMESTAMP nullable,
  queue_date DATE,
  timestamps

reviews
  id,
  queue_id (FK → queues, unique — 1 antrian = 1 review),
  rating TINYINT (1–5),
  comment TEXT nullable,
  submitted_by ENUM('customer','front_office'),
  timestamps

activity_logs
  id, user_id (FK nullable — null jika sistem),
  action VARCHAR(100),        -- contoh: AUTO_CANCEL, VERIFY_CHECKIN, DELETE_SERVICE
  model_type VARCHAR(100),    -- contoh: Booking, Queue, Department
  model_id BIGINT,
  description TEXT nullable,
  created_at TIMESTAMP
  -- TIDAK ADA updated_at; append-only
  -- Tidak bisa dihapus oleh siapapun kecuali super_admin

settings
  id, key VARCHAR(100) unique, value TEXT, description VARCHAR(255),
  updated_by (FK → users nullable), updated_at TIMESTAMP
```

### 4.2 Relasi Kunci

```
departments  ──< counters
departments  ──< services ──< requirements
services     ──< schedules
services     ──< bookings >── users(customer)
bookings     ──< queues
visitors     ──< queues
counters     ──< queues
queues       ──  reviews (one-to-one)
```

### 4.3 Constraint Penting

- `queue_number` unik per `counter_id` per `queue_date`.
- `booking_code` adalah UUID v4, generated saat booking dibuat.
- `reviews.queue_id` adalah `UNIQUE` — mencegah double review.
- `activity_logs` tidak memiliki kolom `updated_at`, tidak ada soft delete.
- `settings.key` adalah `UNIQUE`.

---

## 5. FORMAT NOMOR ANTRIAN

**Format:** `[INISIAL_GERAI]-[NOMOR_URUT_3_DIGIT]`

Reset setiap hari. `NOMOR_URUT` dihitung dari jumlah antrian hari itu di counter tersebut + 1.

### Daftar Inisial Resmi 27 Gerai

| # | Nama Gerai | Inisial |
|---|---|---|
| 1 | DPMPTSPNaker | DPTK |
| 2 | BNNK | BNNK |
| 3 | Sawahlunto Siap Kerja | SSK |
| 4 | ATR/BPN | BPN |
| 5 | PLN | PLN |
| 6 | PDAM | PDAM |
| 7 | Klinik LKPM | KLPM |
| 8 | TASPEN | TS |
| 9 | PT Pos Indonesia | POS |
| 10 | LECI | LECI |
| 11 | BPJS Kesehatan | BPJSK |
| 12 | BPJS Tenaga Kerja | BPJSTK |
| 13 | KP2KP Sawahlunto | KP2KP |
| 14 | Bank Nagari | BNR |
| 15 | Samsat | SMST |
| 16 | BPKAD | BPKAD |
| 17 | LPSE | LPSE |
| 18 | PPID | PPID |
| 19 | Loka POM | LPOM |
| 20 | Klinik Rumah Swadaya | KRS |
| 21 | Kemenag Sawahlunto | KMG |
| 22 | Pengadilan Negeri Sawahlunto | PN |
| 23 | Disdukcapil | DDK |
| 24 | SPKT | SPKT |
| 25 | SKCK | SKCK |
| 26 | Kejaksaan Negeri Sawahlunto | KJKS |
| 27 | Kantor Imigrasi Kelas I TPI Padang | IMI |

---

## 6. FITUR & REQUIREMENTS FUNGSIONAL

### F1 — Reservasi Online (Customer)

| REQ | Deskripsi |
|---|---|
| REQ-1.1 | Customer login dapat reservasi pada jadwal yang masih ada kuota |
| REQ-1.2 | Sistem generate UUID v4 sebagai `booking_code` dan kirim ke email customer |
| REQ-1.3 | Halaman "Reservasi Saya" menampilkan semua booking + status terkini |
| REQ-1.4 | Jika kuota penuh → tolak booking + tampilkan pesan error informatif |
| REQ-1.5 | Satu NIK maks 1 booking aktif (`Pending`) per layanan per hari |
| REQ-1.6 | Pembatalan booking **hanya bisa dilakukan oleh `front_office`** |
| REQ-1.7 *(baru)* | Booking berstatus `Pending` yang belum di-check-in otomatis menjadi `Cancelled` pada pukul **23:59** via Laravel Scheduler (`bookings:cancel-expired`). Dicatat di `activity_logs` dengan `action = AUTO_CANCEL`. |

### F2 — Assisted Check-in & Verifikasi (Front Office)

| REQ | Deskripsi |
|---|---|
| REQ-2.1 | FO dapat mencari booking berdasarkan NIK atau `booking_code` |
| REQ-2.2 | FO mendaftarkan walk-in dengan field: Nama Lengkap, NIK, No. Telepon, Keperluan Kunjungan → insert ke `visitors` + `queues` sekaligus |
| REQ-2.3 | Sistem validasi kuota di `schedules.quota_used` sebelum terbitkan antrian. Jika penuh → tampilkan "Kuota layanan untuk hari ini telah penuh" |
| REQ-2.4 | Nomor antrian format `[INISIAL]-[001]`, unik per counter per hari |

### F3 — Manajemen Data Master (Super Admin)

CRUD penuh untuk: `departments`, `services`, `requirements`, `counters`, `schedules`.

| REQ | Deskripsi |
|---|---|
| REQ-3.1 | CRUD `departments` (termasuk field `inisial`) |
| REQ-3.2 | CRUD `services` berelasi ke `department` |
| REQ-3.3 | CRUD `requirements` berelasi ke `service` |
| REQ-3.4 | CRUD `counters` berelasi ke `department` |
| REQ-3.5 | CRUD `schedules` per layanan per hari (kuota harian) |

### F4 — Manajemen Pengguna & Akun

| REQ | Deskripsi |
|---|---|
| REQ-4.1 | Super Admin CRUD akun pengguna internal di `users` |
| REQ-4.2 | Super Admin set/ubah role pengguna |
| REQ-4.3 | Registrasi mandiri Customer via halaman publik (nama, email, NIK, password) |
| REQ-4.4 | Validasi uniqueness `email` dan `nik` saat registrasi |
| REQ-4.5 *(baru)* | **Semua role** dapat reset password via email (link token expire 60 menit). Menggunakan Laravel built-in password reset (`password_reset_tokens`). |

### F5 — Operasional Pemanggilan Antrian (Gerai)

| REQ | Deskripsi |
|---|---|
| REQ-5.1 | Tampilkan daftar antrian `Waiting` milik counter petugas yang login |
| REQ-5.2 | Petugas gerai ubah status → `Serving`, `Completed`, atau `Skipped` |
| REQ-5.3 | Catat `called_at` saat `Serving`, `completed_at` saat `Completed` |
| REQ-5.4 | Petugas Gerai A tidak bisa lihat/modifikasi antrian Gerai B (Laravel Policy) |

### F6 — Display Monitor (Publik, Tanpa Login)

| REQ | Deskripsi |
|---|---|
| REQ-6.1 | URL `/display` accessible tanpa auth, tampilkan antrian `Serving` per loket |
| REQ-6.2 | Auto-refresh via AJAX polling setiap **5 detik** |
| REQ-6.3 | Marquee teks berjalan dari `settings` key `marquee_text`, dapat dikonfigurasi Super Admin |

### F7 — Umpan Balik Masyarakat

| REQ | Deskripsi |
|---|---|
| REQ-7.1 | Customer dengan tiket `Completed` dapat beri rating bintang 1–5 + komentar |
| REQ-7.2 | FO dapat input umpan balik atas nama pengunjung walk-in (`submitted_by = 'front_office'`) |
| REQ-7.3 | Statistik rata-rata bintang per gerai + total umpan balik tampil di dasbor Admin |
| REQ-7.4 | Satu tiket = satu umpan balik. Tidak dapat diubah/dihapus setelah submit |

### F8 — Pelaporan & Analitik (Super Admin)

| REQ | Deskripsi |
|---|---|
| REQ-8.1 | Laporan kunjungan harian, filter by tanggal + gerai, tampil tabel + grafik |
| REQ-8.2 | Export rekap pengunjung (nama, NIK, gerai, tanggal) ke **PDF** (`barryvdh/laravel-dompdf`) atau **Excel** (`maatwebsite/laravel-excel`) |
| REQ-8.3 | Laporan hanya bisa diakses role `super_admin` |

---

## 7. BUSINESS RULES

```
BR-01  Customer hanya terima Kode Booking (UUID), bukan nomor antrian.
BR-02  Nomor antrian aktif hanya diterbitkan oleh front_office.
BR-03  Kuota dikelola via schedules. Slot otomatis tutup saat quota_used >= quota_total.
BR-04  Umpan balik hanya bisa diberikan jika status antrian = Completed.
BR-05  Umpan balik tidak dapat diubah atau dihapus setelah dikirim.
BR-06  Satu NIK = maks 1 booking aktif (Pending) per layanan per hari.
BR-07  Walk-in yang sudah dapat nomor antrian aktif hari ini untuk layanan yang sama
       tidak dapat didaftarkan ulang oleh FO.
BR-08  Pembatalan booking hanya oleh front_office, bukan customer.
BR-09  Booking Pending otomatis Cancelled pukul 23:59 via scheduler.
BR-10  activity_logs adalah append-only. Hanya super_admin yang bisa hapus log.
BR-11  Kegagalan SMTP tidak boleh membatalkan proses booking (try-catch di Mailable).
BR-12  Rating menggunakan skala bintang 1–5. Tidak ada klasifikasi sentimen otomatis.
```

---

## 8. NON-FUNCTIONAL REQUIREMENTS

| Kategori | Requirement |
|---|---|
| Performance | Page load portal publik ≤ 3 detik pada koneksi 10 Mbps |
| Performance | Display monitor update ≤ 5 detik setelah status berubah |
| Performance | Sistem handle min. 50 concurrent users |
| Performance | Pencarian booking oleh FO < 2 detik |
| Security | Password di-hash Bcrypt; plaintext dilarang |
| Security | OWASP Top 10: SQL Injection (Eloquent ORM) + XSS (Blade auto-escape) dicegah |
| Security | Semua form dilindungi CSRF token Laravel |
| Security | Semua rute terautentikasi dilindungi middleware `auth` |
| Security | RBAC via Laravel Gates/Policies |
| Security | Seluruh komunikasi via HTTPS/TLS |
| Reliability | Kegagalan email tidak batalkan proses booking |
| Auditability | Semua aksi kritis dicatat di `activity_logs` (user_id, action, model, timestamp) |
| Usability | Proses check-in FO selesai dalam maks 3 langkah interaksi |
| Usability | Semua label dalam Bahasa Indonesia |
| Usability | Custom error page HTTP 404 dan 500; stack trace tidak tampil ke user |
| Usability | Semua aksi destruktif (hapus) wajib dialog konfirmasi |
| Maintainability | Pola MVC Laravel; logika bisnis di Service classes |
| Portability | Konfigurasi via `.env`; zero hardcoded credential |
| Testability | Setiap modul CRUD punya min. 1 skenario Black-Box (valid + invalid) |

---

## 9. HALAMAN & ROUTE UTAMA

| Area | Route Prefix | Auth |
|---|---|---|
| Portal publik (info layanan) | `/` | Tidak perlu |
| Registrasi & login | `/register`, `/login` | Guest |
| Reset password | `/forgot-password`, `/reset-password/{token}` | Guest |
| Dasbor Customer | `/customer/*` | `auth` + role `customer` |
| Dasbor Front Office | `/fo/*` | `auth` + role `front_office` |
| Dasbor Gerai | `/gerai/*` | `auth` + role `gerai` |
| Dasbor Super Admin | `/admin/*` | `auth` + role `super_admin` |
| Display monitor | `/display` | **Tidak perlu** |

---

## 10. ARTISAN COMMANDS & SCHEDULER

```bash
# Command wajib dibuat:
php artisan make:command CancelExpiredBookings
# Signature: bookings:cancel-expired
# Logic: UPDATE bookings SET status='Cancelled'
#        WHERE status='Pending' AND booking_date < TODAY()
#        Catat tiap record ke activity_logs (action=AUTO_CANCEL, user_id=null)

# Dijadwalkan di routes/console.php atau Kernel.php:
Schedule::command('bookings:cancel-expired')->dailyAt('23:59');

# Cron entry di VPS:
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 11. SEEDER & DATA AWAL

```
DatabaseSeeder
├── SettingsSeeder        → marquee_text default, marquee_active=true
├── DepartmentSeeder      → 27 gerai + inisial (lihat Section 5)
├── UserSeeder            → 1 super_admin, 1 front_office, gerai per counter
├── ServiceSeeder         → min. 10 layanan (menunggu data dari mitra — TBD9)
├── ScheduleSeeder        → jadwal + kuota untuk demo
└── QueueHistorySeeder    → min. 50 data antrian historis untuk demo/testing
```

---

## 12. STATUS TBD

| TBD | Status | Resolusi |
|---|---|---|
| TBD1 — Analisis sentimen | ✅ RESOLVED | Tidak diimplementasi; form bintang biasa |
| TBD2 — Real-time display | ✅ RESOLVED | AJAX polling 5 detik |
| TBD3 — Format laporan | ✅ RESOLVED | REQ-8.1 & 8.2 |
| TBD4 — Field walk-in | ✅ RESOLVED | NIK, Nama, NoHP, Keperluan |
| TBD5 — Mekanisme batal booking | ✅ RESOLVED | Hanya FO |
| TBD6 — Nama resmi sistem | ⏳ OPEN | Target: sebelum Milestone 2 |
| TBD7 — Inisial resmi gerai | ✅ RESOLVED | Lihat tabel Section 5 (menunggu tanda tangan mitra) |
| TBD8 — Koneksi internet gerai | ⏳ OPEN | FO sudah konfirmasi 100 Mbps; status gerai lain menyusul |
| TBD9 — Data layanan per gerai | ⏳ OPEN | Daftar 27 instansi sudah diterima; detail layanan menyusul |

---

## 13. GLOSSARY TEKNIS SINGKAT

| Istilah | Arti |
|---|---|
| MPP | Mal Pelayanan Publik — layanan terpadu satu atap |
| FO | Front Office — petugas verifikator & penerbit antrian aktif |
| Assisted Check-in | Verifikasi fisik reservasi online oleh FO sebelum antrian diterbitkan |
| Kode Booking | UUID v4 — bukti reservasi, **bukan** nomor antrian |
| Walk-in | Pengunjung tanpa reservasi; didaftarkan langsung oleh FO |
| RBAC | Role-Based Access Control via Laravel Gates/Policies |
| CRUD | Create, Read, Update, Delete |
| Inisial Gerai | Kode prefix nomor antrian, maks 6 char, unik per counter |