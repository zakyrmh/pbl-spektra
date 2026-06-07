# Test Case — Halaman Manajemen Pengguna

**Metode**: White-Box Testing  
**Halaman Uji**: `/manajemen-pengguna` (super_admin/users/index.blade.php)  
**Versi Aplikasi**: MPP Kota Sawahlunto  
**Tanggal Dibuat**: 2026-06-07  
**Dibuat oleh**: QA Engineer

---

> [!IMPORTANT]
> **Metode White-Box Testing** menguji logika internal kode, bukan hanya antarmuka. Setiap test case memetakan ke **path kode spesifik** di controller, model, atau JavaScript yang dieksekusi. Teknik yang digunakan: _Statement Coverage_, _Branch Coverage_, dan _Path Coverage_.

---

## Peta Komponen yang Diuji

| Komponen                        | File                 | Baris Kunci |
| ------------------------------- | -------------------- | ----------- |
| Controller `index()`            | `UserController.php` | L22–76      |
| Controller `store()`            | `UserController.php` | L83–105     |
| Controller `update()`           | `UserController.php` | L112–153    |
| Controller `toggleStatus()`     | `UserController.php` | L160–171    |
| Controller `resetPassword()`    | `UserController.php` | L178–193    |
| Controller `destroy()`          | `UserController.php` | L200–212    |
| Model Scope `online()`          | `User.php`           | L95–98      |
| Model `isOnline()`              | `User.php`           | L136–140    |
| JavaScript `fetchUsers()`       | `index.blade.php`    | L432–497    |
| JavaScript `handleRoleChange()` | `index.blade.php`    | L663–680    |
| JavaScript `openEditModal()`    | `index.blade.php`    | L600–626    |
| View partial table              | `table.blade.php`    | L1–208      |

---

## Area 1 — Statistik Cards (Metrics)

> **Kode yang diuji**: `UserController@index` L27–33 dan `User` scopes

### TC-M-01 — Total Pengguna: Hitungan Valid

| Atribut       | Detail                                    |
| ------------- | ----------------------------------------- |
| **ID**        | TC-M-01                                   |
| **Fitur**     | Card Total Pengguna                       |
| **Teknik**    | Statement Coverage                        |
| **Path Kode** | `UserController.php:27` → `User::count()` |
| **Prioritas** | Tinggi                                    |

**Precondition**:

- Login sebagai Super Admin
- Database memiliki sejumlah pengguna (mis. N pengguna)

**Langkah Uji**:

1. Catat jumlah baris di tabel `users` via SQL: `SELECT COUNT(*) FROM users;`
2. Buka halaman `/manajemen-pengguna`
3. Amati angka pada card **"Total Pengguna"**

**Expected Result**:

- Angka pada card = hasil query SQL `COUNT(*)` di atas
- Tidak ada selisih satu pun

**Verification Query**:

```sql
SELECT COUNT(*) AS total FROM users;
```

---

### TC-M-02 — Total Pengguna: Angka Bertambah Setelah Tambah User

| Atribut       | Detail                                       |
| ------------- | -------------------------------------------- |
| **ID**        | TC-M-02                                      |
| **Fitur**     | Card Total Pengguna — Sinkronisasi Real-time |
| **Teknik**    | Statement Coverage                           |
| **Path Kode** | `User::count()` setelah `User::create()`     |
| **Prioritas** | Tinggi                                       |

**Precondition**: N pengguna ada di database

**Langkah Uji**:

1. Catat angka Total Pengguna di halaman (N)
2. Tambah satu pengguna baru via tombol **"Tambah Pengguna"**
3. Reload halaman `/manajemen-pengguna`
4. Amati angka card Total Pengguna

**Expected Result**: Angka berubah menjadi N+1

---

### TC-M-03 — Total Pengguna: Angka Berkurang Setelah Hapus User

| Atribut       | Detail                                                               |
| ------------- | -------------------------------------------------------------------- |
| **ID**        | TC-M-03                                                              |
| **Fitur**     | Card Total Pengguna — Sinkronisasi Real-time                         |
| **Teknik**    | Branch Coverage                                                      |
| **Path Kode** | `UserController@destroy` → `User::delete()` → reload `User::count()` |
| **Prioritas** | Tinggi                                                               |

**Precondition**: N pengguna ada di database

**Langkah Uji**:

1. Catat angka Total Pengguna (N)
2. Hapus satu pengguna via menu Aksi → Hapus Pengguna
3. Reload halaman
4. Amati angka card Total Pengguna

**Expected Result**: Angka berubah menjadi N−1

---

### TC-M-04 — Staf Aktif (Online): Hitungan Valid

| Atribut       | Detail                                                                                |
| ------------- | ------------------------------------------------------------------------------------- |
| **ID**        | TC-M-04                                                                               |
| **Fitur**     | Card Staf Aktif (Online)                                                              |
| **Teknik**    | Statement + Branch Coverage                                                           |
| **Path Kode** | `UserController.php:28–30` → `User::online()->whereIn('role', staffRoles())->count()` |
| **Prioritas** | Tinggi                                                                                |

**Precondition**: Ada beberapa user staf (super_admin, admin_fo, admin_gerai) dengan `last_login_at` bervariasi

**Langkah Uji**:

1. Hitung via SQL:
    ```sql
    SELECT COUNT(*) FROM users
    WHERE last_login_at >= NOW() - INTERVAL 15 MINUTE
    AND role IN ('super_admin', 'admin_fo', 'admin_gerai');
    ```
2. Buka halaman `/manajemen-pengguna`
3. Bandingkan angka card "Staf Aktif (Online)" dengan hasil SQL

**Expected Result**: Angka card = hasil query SQL

**Catatan White-Box**:

- Model scope `online()` → `where('last_login_at', '>=', now()->subMinutes(15))` — L95–98 `User.php`
- `staffRoles()` mengembalikan `[SuperAdmin, AdminFo, AdminGerai]` — L49–52 `UserRole.php`
- Role `pengunjung` **tidak** dihitung sebagai staf → Branch FALSE harus dikecualikan

---

### TC-M-05 — Staf Aktif: Pengunjung Tidak Dihitung

| Atribut       | Detail                                                  |
| ------------- | ------------------------------------------------------- |
| **ID**        | TC-M-05                                                 |
| **Fitur**     | Card Staf Aktif (Online) — Validasi Eksklusif Role      |
| **Teknik**    | Branch Coverage (sisi FALSE dari `whereIn`)             |
| **Path Kode** | `UserRole::staffRoles()` — nilai enum yang dikembalikan |
| **Prioritas** | Sedang                                                  |

**Precondition**: Ada pengguna dengan role `pengunjung` yang baru login (last_login_at dalam 15 menit)

**Langkah Uji**:

1. Update `last_login_at` user pengunjung ke NOW():
    ```sql
    UPDATE users SET last_login_at = NOW() WHERE role = 'pengunjung' LIMIT 1;
    ```
2. Hitung staf online via SQL (tanpa pengunjung)
3. Buka halaman dan bandingkan angka card

**Expected Result**: Angka card tidak berubah (pengunjung tidak dihitung)

---

### TC-M-06 — Total Instansi/Loket: Hitungan DISTINCT Valid

| Atribut       | Detail                                                                                                 |
| ------------- | ------------------------------------------------------------------------------------------------------ |
| **ID**        | TC-M-06                                                                                                |
| **Fitur**     | Card Total Instansi / Loket                                                                            |
| **Teknik**    | Statement Coverage                                                                                     |
| **Path Kode** | `UserController.php:31–33` → `User::whereNotNull('instansi')->distinct('instansi')->count('instansi')` |
| **Prioritas** | Tinggi                                                                                                 |

**Precondition**: Ada beberapa pengguna dengan kolom `instansi` terisi

**Langkah Uji**:

1. Hitung via SQL:
    ```sql
    SELECT COUNT(DISTINCT instansi) FROM users WHERE instansi IS NOT NULL;
    ```
2. Buka halaman `/manajemen-pengguna`
3. Bandingkan angka card "Total Instansi / Loket"

**Expected Result**: Angka card = hasil query SQL

**Catatan White-Box**: Jika 5 user mendaftar dengan instansi yang sama (mis. 'Disdukcapil'), maka instansi tersebut hanya dihitung 1x karena menggunakan `DISTINCT`.

---

### TC-M-07 — Total Instansi: User Tanpa Instansi Tidak Dihitung

| Atribut       | Detail                                           |
| ------------- | ------------------------------------------------ |
| **ID**        | TC-M-07                                          |
| **Fitur**     | Card Total Instansi — Branch `whereNotNull`      |
| **Teknik**    | Branch Coverage (sisi FALSE: `instansi IS NULL`) |
| **Path Kode** | `User::whereNotNull('instansi')`                 |
| **Prioritas** | Sedang                                           |

**Precondition**: Ada pengguna dengan `instansi = NULL` (mis. Super Admin, Pengunjung)

**Langkah Uji**:

1. Hitung user dengan instansi NULL: `SELECT COUNT(*) FROM users WHERE instansi IS NULL;`
2. Tambah user baru dengan role `super_admin` (instansi kosong)
3. Reload halaman dan periksa card Total Instansi

**Expected Result**: Angka card tidak bertambah karena user baru tanpa instansi tidak dihitung

---

## Area 2 — Fitur Live Search

> **Kode yang diuji**: `UserController@index` L38–45, JS `fetchUsers()` L432–497, JS debounce L522–531

### TC-S-01 — Search: Ditemukan Berdasarkan Nama

| Atribut       | Detail                                                              |
| ------------- | ------------------------------------------------------------------- |
| **ID**        | TC-S-01                                                             |
| **Fitur**     | Live Search — Filter Nama                                           |
| **Teknik**    | Path Coverage (`$request->filled('search')` = TRUE)                 |
| **Path Kode** | `UserController.php:38–45` → `where('name', 'like', "%{$search}%")` |
| **Prioritas** | Tinggi                                                              |

**Precondition**: Ada pengguna dengan nama "Budi Santoso" di database

**Langkah Uji**:

1. Buka `/manajemen-pengguna`
2. Ketik "Budi" pada input pencarian
3. Tunggu 300ms (debounce)
4. Amati hasil tabel yang diperbarui

**Expected Result**:

- Tabel menampilkan baris yang mengandung "Budi" pada nama
- Baris pengguna yang tidak mengandung "Budi" pada nama, email, atau NIK **tidak** ditampilkan
- Info count diperbarui (mis. "Menampilkan 1–X dari Y pengguna")

**Validation Query**:

```sql
SELECT * FROM users WHERE name LIKE '%Budi%'
OR email LIKE '%Budi%' OR nik LIKE '%Budi%';
```

---

### TC-S-02 — Search: Ditemukan Berdasarkan Email

| Atribut       | Detail                                          |
| ------------- | ----------------------------------------------- |
| **ID**        | TC-S-02                                         |
| **Fitur**     | Live Search — Filter Email                      |
| **Teknik**    | Path Coverage → `orWhere('email', 'like', ...)` |
| **Path Kode** | `UserController.php:42`                         |
| **Prioritas** | Tinggi                                          |

**Langkah Uji**:

1. Ketik domain email "@disdukcapil" pada input pencarian
2. Tunggu debounce (300ms)
3. Amati tabel

**Expected Result**: Hanya pengguna dengan email mengandung "@disdukcapil" yang tampil

---

### TC-S-03 — Search: Ditemukan Berdasarkan NIK

| Atribut       | Detail                                        |
| ------------- | --------------------------------------------- |
| **ID**        | TC-S-03                                       |
| **Fitur**     | Live Search — Filter NIK                      |
| **Teknik**    | Path Coverage → `orWhere('nik', 'like', ...)` |
| **Path Kode** | `UserController.php:43`                       |
| **Prioritas** | Tinggi                                        |

**Precondition**: Ada pengguna dengan NIK `1372xxxxxxxxxx`

**Langkah Uji**:

1. Ketik 4–6 digit pertama NIK pada input pencarian (mis. "1372")
2. Tunggu debounce
3. Amati tabel

**Expected Result**: Pengguna dengan NIK yang mengandung "1372" ditampilkan

---

### TC-S-04 — Search: Tidak Ada Hasil

| Atribut       | Detail                                         |
| ------------- | ---------------------------------------------- |
| **ID**        | TC-S-04                                        |
| **Fitur**     | Live Search — Empty State                      |
| **Teknik**    | Branch Coverage → `@forelse` → `@empty` branch |
| **Path Kode** | `table.blade.php:183–196` (empty state)        |
| **Prioritas** | Tinggi                                         |

**Langkah Uji**:

1. Ketik string yang tidak ada di database, mis. "xxxxxxxxxzzzz"
2. Tunggu debounce

**Expected Result**:

- Tabel menampilkan empty state: _"Tidak ada pengguna ditemukan"_
- Teks "Coba ubah kata kunci pencarian atau filter yang digunakan." muncul
- Link "Reset semua filter" tersedia

---

### TC-S-05 — Search: Debounce 300ms Berjalan

| Atribut       | Detail                             |
| ------------- | ---------------------------------- |
| **ID**        | TC-S-05                            |
| **Fitur**     | Live Search — Debounce JavaScript  |
| **Teknik**    | Path Coverage → JS `debounceTimer` |
| **Path Kode** | `index.blade.php:522–531`          |
| **Prioritas** | Sedang                             |

**Langkah Uji**:

1. Buka DevTools Network tab
2. Ketik "Budi" secara cepat (5 karakter dalam <300ms)
3. Amati jumlah request HTTP ke `/manajemen-pengguna`

**Expected Result**:

- Hanya 1 request AJAX yang dikirim (bukan 5 request per karakter)
- Request dikirim ~300ms setelah ketikan terakhir berhenti

---

### TC-S-06 — Search: Request AJAX Mengembalikan JSON

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-S-06                                                      |
| **Fitur**     | Live Search — AJAX Response                                  |
| **Teknik**    | Path Coverage → `$request->ajax()` atau `has('ajax')` = TRUE |
| **Path Kode** | `UserController.php:61–68`                                   |
| **Prioritas** | Tinggi                                                       |

**Langkah Uji**:

1. Buka DevTools Network tab
2. Lakukan pencarian apapun
3. Periksa response dari request `/manajemen-pengguna?search=xxx&ajax=1`

**Expected Result**:

- Status HTTP 200
- Content-Type: `application/json`
- Body JSON mengandung key `html` (string HTML tabel) dan `info` (string info jumlah)
- `html` berisi markup yang valid dari `table.blade.php`

---

### TC-S-07 — Search: URL Browser Diperbarui

| Atribut       | Detail                                             |
| ------------- | -------------------------------------------------- |
| **ID**        | TC-S-07                                            |
| **Fitur**     | Live Search — History.pushState                    |
| **Teknik**    | Statement Coverage → JS `window.history.pushState` |
| **Path Kode** | `index.blade.php:486–488`                          |
| **Prioritas** | Sedang                                             |

**Langkah Uji**:

1. Ketik "Budi" pada pencarian
2. Amati URL di address bar browser

**Expected Result**:

- URL berubah menjadi `/manajemen-pengguna?search=Budi`
- Parameter `ajax` **tidak** muncul di URL (dihapus via `cleanUrl.searchParams.delete('ajax')`)

---

### TC-S-08 — Search: Loading State Muncul

| Atribut       | Detail                                                |
| ------------- | ----------------------------------------------------- |
| **ID**        | TC-S-08                                               |
| **Fitur**     | Live Search — Loading Micro-animation                 |
| **Teknik**    | Statement Coverage → JS `classList.add('opacity-40')` |
| **Path Kode** | `index.blade.php:439`                                 |
| **Prioritas** | Rendah                                                |

**Langkah Uji**:

1. Throttle jaringan ke "Slow 3G" di DevTools
2. Lakukan pencarian
3. Amati container tabel selama request berlangsung

**Expected Result**:

- Container tabel menjadi semi-transparan (opacity 40%) selama loading
- Setelah response diterima, opacity kembali normal

---

## Area 3 — Fitur Filter

> **Kode yang diuji**: `UserController@index` L47–57

### TC-F-01 — Filter Instansi: Berhasil Menyaring

| Atribut       | Detail                                                                       |
| ------------- | ---------------------------------------------------------------------------- |
| **ID**        | TC-F-01                                                                      |
| **Fitur**     | Filter Instansi                                                              |
| **Teknik**    | Branch Coverage → `$request->filled('instansi')` = TRUE                      |
| **Path Kode** | `UserController.php:47–49` → `$query->where('instansi', $request->instansi)` |
| **Prioritas** | Tinggi                                                                       |

**Precondition**: Ada pengguna dengan instansi "Disdukcapil" dan ada pengguna dengan instansi lain

**Langkah Uji**:

1. Pilih "Dinas Kependudukan & Pencatatan Sipil" pada dropdown Instansi
2. Amati tabel (AJAX otomatis terpicu via `onchange="fetchUsers()"`)

**Expected Result**:

- Tabel hanya menampilkan pengguna dengan `instansi = 'Disdukcapil'`
- Pengguna dari instansi lain tidak muncul

**Validation Query**:

```sql
SELECT COUNT(*) FROM users WHERE instansi = 'Disdukcapil';
```

---

### TC-F-02 — Filter Instansi: Semua Instansi (Reset)

| Atribut       | Detail                                                           |
| ------------- | ---------------------------------------------------------------- |
| **ID**        | TC-F-02                                                          |
| **Fitur**     | Filter Instansi — Reset                                          |
| **Teknik**    | Branch Coverage → `$request->filled('instansi')` = FALSE         |
| **Path Kode** | `UserController.php:47` — kondisi FALSE, filter tidak diterapkan |
| **Prioritas** | Sedang                                                           |

**Langkah Uji**:

1. Pilih "Semua Instansi" (value kosong) pada dropdown Instansi
2. Amati tabel

**Expected Result**: Seluruh pengguna dari semua instansi ditampilkan (tanpa filter instansi)

---

### TC-F-03 — Filter Role: Berhasil Menyaring

| Atribut       | Detail                                                               |
| ------------- | -------------------------------------------------------------------- |
| **ID**        | TC-F-03                                                              |
| **Fitur**     | Filter Peran (Role)                                                  |
| **Teknik**    | Branch Coverage → `$request->filled('role') && in_array(...)` = TRUE |
| **Path Kode** | `UserController.php:51–53`                                           |
| **Prioritas** | Tinggi                                                               |

**Langkah Uji** (per role):

1. Pilih "Super Admin" → Verifikasi hanya `role='super_admin'` yang tampil
2. Pilih "Admin Front Office" → Verifikasi hanya `role='admin_fo'`
3. Pilih "Operator Loket" → Verifikasi hanya `role='admin_gerai'`
4. Pilih "Pengunjung" → Verifikasi hanya `role='pengunjung'`

**Expected Result per langkah**: Tabel hanya berisi pengguna dengan role yang dipilih

---

### TC-F-04 — Filter Role: Role Tidak Valid Diabaikan

| Atribut       | Detail                                                                   |
| ------------- | ------------------------------------------------------------------------ |
| **ID**        | TC-F-04                                                                  |
| **Fitur**     | Filter Role — Validasi Enum                                              |
| **Teknik**    | Branch Coverage → `in_array($request->role, UserRole::values())` = FALSE |
| **Path Kode** | `UserController.php:51` — kondisi `in_array` gagal                       |
| **Prioritas** | Sedang                                                                   |

**Langkah Uji**:

1. Manipulasi request secara manual (via Postman/curl): `GET /manajemen-pengguna?role=hacker`
2. Amati response

**Expected Result**: Semua pengguna ditampilkan (filter role diabaikan karena nilai tidak valid)

---

### TC-F-05 — Filter Status: Aktif

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-F-05                                                         |
| **Fitur**     | Filter Status — Aktif                                           |
| **Teknik**    | Branch Coverage → `$request->status === 'aktif'` = TRUE         |
| **Path Kode** | `UserController.php:55–57` → `$query->where('is_active', true)` |
| **Prioritas** | Tinggi                                                          |

**Langkah Uji**:

1. Pilih "Aktif" pada dropdown Status
2. Amati tabel

**Expected Result**: Hanya pengguna dengan `is_active = 1` yang muncul di tabel

---

### TC-F-06 — Filter Status: Nonaktif

| Atribut       | Detail                                                                         |
| ------------- | ------------------------------------------------------------------------------ |
| **ID**        | TC-F-06                                                                        |
| **Fitur**     | Filter Status — Nonaktif                                                       |
| **Teknik**    | Branch Coverage → `$request->status === 'aktif'` = FALSE (status = 'nonaktif') |
| **Path Kode** | `UserController.php:56` → `$query->where('is_active', false)`                  |
| **Prioritas** | Tinggi                                                                         |

**Langkah Uji**:

1. Pilih "Nonaktif" pada dropdown Status
2. Amati tabel

**Expected Result**: Hanya pengguna dengan `is_active = 0` yang muncul

---

### TC-F-07 — Filter Kombinasi: Search + Role + Status

| Atribut       | Detail                                                |
| ------------- | ----------------------------------------------------- |
| **ID**        | TC-F-07                                               |
| **Fitur**     | Filter Kombinasi                                      |
| **Teknik**    | Path Coverage — seluruh cabang filter aktif bersamaan |
| **Path Kode** | `UserController.php:38–57` — semua kondisi TRUE       |
| **Prioritas** | Tinggi                                                |

**Langkah Uji**:

1. Ketik "admin" pada input pencarian
2. Pilih "Admin Front Office" pada filter Role
3. Pilih "Aktif" pada filter Status
4. Amati tabel

**Expected Result**:

- Hanya pengguna yang memenuhi **semua** kriteria:
    - Nama/email/NIK mengandung "admin"
    - Role = `admin_fo`
    - `is_active = 1`

**Validation Query**:

```sql
SELECT * FROM users
WHERE (name LIKE '%admin%' OR email LIKE '%admin%' OR nik LIKE '%admin%')
AND role = 'admin_fo' AND is_active = 1;
```

---

### TC-F-08 — Tombol Reset Filter

| Atribut       | Detail                                                           |
| ------------- | ---------------------------------------------------------------- |
| **ID**        | TC-F-08                                                          |
| **Fitur**     | Tombol Reset Filter                                              |
| **Teknik**    | Statement Coverage → JS event listener pada `#btn-reset-filters` |
| **Path Kode** | `index.blade.php:557–570`                                        |
| **Prioritas** | Sedang                                                           |

**Langkah Uji**:

1. Terapkan beberapa filter (search + instansi + role)
2. Klik tombol "X" (Reset Filter)
3. Amati form dan tabel

**Expected Result**:

- Semua field filter dikosongkan
- Tombol reset menjadi tersembunyi
- Tabel menampilkan semua pengguna tanpa filter

---

### TC-F-09 — Tombol Reset Filter: Tersembunyi Saat Tidak Ada Filter

| Atribut       | Detail                                                                          |
| ------------- | ------------------------------------------------------------------------------- |
| **ID**        | TC-F-09                                                                         |
| **Fitur**     | Tombol Reset Filter — Visibilitas Awal                                          |
| **Teknik**    | Branch Coverage → `request()->hasAny(['search', 'instansi', 'role', 'status'])` |
| **Path Kode** | `index.blade.php:194` — Blade inline `style="display: ..."`                     |
| **Prioritas** | Rendah                                                                          |

**Langkah Uji**:

1. Buka `/manajemen-pengguna` tanpa query parameter
2. Amati tombol Reset Filter

**Expected Result**: Tombol reset **tidak terlihat** (`display: none`)

---

## Area 4 — Tabel Pengguna

> **Kode yang diuji**: `table.blade.php` seluruhnya + `User` accessors + `UserRole` enum

### TC-T-01 — Tabel: Kolom Nama & Email Tampil

| Atribut       | Detail                  |
| ------------- | ----------------------- |
| **ID**        | TC-T-01                 |
| **Fitur**     | Tabel — Kolom Pengguna  |
| **Teknik**    | Statement Coverage      |
| **Path Kode** | `table.blade.php:34–38` |
| **Prioritas** | Tinggi                  |

**Langkah Uji**:

1. Buka halaman, amati setiap baris tabel
2. Periksa apakah nama dan email ditampilkan sesuai data di database

**Expected Result**:

- Nama ditampilkan via `{{ $user->name }}`
- Email di bawah nama via `{{ $user->email }}`
- NIK (jika ada) ditampilkan dengan format mono: `NIK: xxxx`

---

### TC-T-02 — Tabel: Badge Role Tampil dengan Warna Benar

| Atribut       | Detail                                                    |
| ------------- | --------------------------------------------------------- |
| **ID**        | TC-T-02                                                   |
| **Fitur**     | Tabel — Badge Peran                                       |
| **Teknik**    | Branch Coverage → `UserRole::badgeClass()` tiap case enum |
| **Path Kode** | `UserRole.php:34–42` + `table.blade.php:45–48`            |
| **Prioritas** | Sedang                                                    |

**Expected Result per role**:
| Role | Label Ditampilkan | Warna Badge |
|------|-------------------|-------------|
| `super_admin` | Super Admin | Merah (`bg-red-100 text-red-700`) |
| `admin_fo` | Admin Front Office | Biru (`bg-blue-100 text-blue-700`) |
| `admin_gerai` | Operator Loket | Ungu (`bg-violet-100 text-violet-700`) |
| `pengunjung` | Pengunjung | Abu-abu (`bg-gray-100 text-gray-600`) |

---

### TC-T-03 — Tabel: Kolom Instansi/Loket — Ada Data

| Atribut       | Detail                                          |
| ------------- | ----------------------------------------------- |
| **ID**        | TC-T-03                                         |
| **Fitur**     | Tabel — Kolom Instansi (Branch: data ada)       |
| **Teknik**    | Branch Coverage → `@if($user->instansi)` = TRUE |
| **Path Kode** | `table.blade.php:52–59`                         |
| **Prioritas** | Sedang                                          |

**Langkah Uji**: Lihat baris user Operator Loket yang memiliki instansi dan nomor loket

**Expected Result**:

- Nama instansi ditampilkan via accessor `$user->instansi_label` (label lengkap, bukan key)
- Nomor loket ditampilkan di bawah nama instansi: "Loket L1"

---

### TC-T-04 — Tabel: Kolom Instansi/Loket — Tidak Ada Data

| Atribut       | Detail                                           |
| ------------- | ------------------------------------------------ |
| **ID**        | TC-T-04                                          |
| **Fitur**     | Tabel — Kolom Instansi (Branch: data kosong)     |
| **Teknik**    | Branch Coverage → `@if($user->instansi)` = FALSE |
| **Path Kode** | `table.blade.php:57–59`                          |
| **Prioritas** | Rendah                                           |

**Langkah Uji**: Lihat baris user Super Admin atau Pengunjung (tanpa instansi)

**Expected Result**: Tampilkan tanda "—" (dash)

---

### TC-T-05 — Tabel: Status Badge Aktif

| Atribut       | Detail                                           |
| ------------- | ------------------------------------------------ |
| **ID**        | TC-T-05                                          |
| **Fitur**     | Tabel — Status Aktif                             |
| **Teknik**    | Branch Coverage → `@if($user->is_active)` = TRUE |
| **Path Kode** | `table.blade.php:64–75`                          |
| **Prioritas** | Tinggi                                           |

**Expected Result**:

- Badge hijau dengan teks "Aktif"
- Dot hijau beranimasi pulse jika user sedang online (`$isOnline = true`)

---

### TC-T-06 — Tabel: Status Badge Nonaktif

| Atribut       | Detail                                            |
| ------------- | ------------------------------------------------- |
| **ID**        | TC-T-06                                           |
| **Fitur**     | Tabel — Status Nonaktif                           |
| **Teknik**    | Branch Coverage → `@if($user->is_active)` = FALSE |
| **Path Kode** | `table.blade.php:69–74`                           |
| **Prioritas** | Tinggi                                            |

**Expected Result**: Badge abu-abu dengan teks "Nonaktif" (tanpa animasi)

---

### TC-T-07 — Tabel: Indikator Online — User Aktif & Baru Login

| Atribut       | Detail                                                        |
| ------------- | ------------------------------------------------------------- |
| **ID**        | TC-T-07                                                       |
| **Fitur**     | Tabel — Indikator Online (dot hijau pada avatar)              |
| **Teknik**    | Branch Coverage → `@if($isOnline && $user->is_active)` = TRUE |
| **Path Kode** | `table.blade.php:29–31` + `User.php:136–140`                  |
| **Prioritas** | Sedang                                                        |

**Precondition**: User dengan `is_active = 1` dan `last_login_at` dalam 15 menit terakhir

**Expected Result**: Dot hijau kecil muncul di pojok kanan bawah avatar pengguna

---

### TC-T-08 — Tabel: Last Login Tampil Benar

| Atribut       | Detail                                        |
| ------------- | --------------------------------------------- |
| **ID**        | TC-T-08                                       |
| **Fitur**     | Tabel — Kolom Last Login                      |
| **Teknik**    | Branch Coverage → `@if($user->last_login_at)` |
| **Path Kode** | `table.blade.php:78–85`                       |
| **Prioritas** | Rendah                                        |

**Expected Result**:

- Jika ada: Format tanggal `d M Y` dan jam `H:i WIB` + relative time
- Jika tidak ada: Teks "Belum pernah login"

---

### TC-T-09 — Tabel: Pagination 10 Data Per Halaman

| Atribut       | Detail                                           |
| ------------- | ------------------------------------------------ |
| **ID**        | TC-T-09                                          |
| **Fitur**     | Tabel — Pagination                               |
| **Teknik**    | Statement Coverage → `paginate(10)`              |
| **Path Kode** | `UserController.php:59` → `$query->paginate(10)` |
| **Prioritas** | Tinggi                                           |

**Precondition**: Database memiliki lebih dari 10 pengguna

**Langkah Uji**:

1. Buka halaman, hitung baris di tabel halaman 1
2. Klik pagination "2"
3. Hitung baris di tabel halaman 2

**Expected Result**:

- Halaman 1: Maksimal 10 baris
- Halaman 2: Baris sisanya (bisa <10)
- Navigasi pagination tampil jika total > 10 (`$users->hasPages()`)

---

### TC-T-10 — Tabel: Pagination AJAX (Tidak Reload Halaman)

| Atribut       | Detail                                                    |
| ------------- | --------------------------------------------------------- |
| **ID**        | TC-T-10                                                   |
| **Fitur**     | Tabel — Pagination via AJAX                               |
| **Teknik**    | Path Coverage → JS event delegation pada pagination links |
| **Path Kode** | `index.blade.php:543–553`                                 |
| **Prioritas** | Tinggi                                                    |

**Langkah Uji**:

1. Buka DevTools Network tab
2. Klik tombol halaman berikutnya di pagination
3. Amati apakah halaman reload penuh atau hanya tabel yang diperbarui

**Expected Result**:

- Halaman **tidak** reload penuh (tidak ada full page reload)
- Hanya konten tabel yang diperbarui via AJAX
- URL berubah dengan parameter `?page=2`

---

## Area 5 — Modal Edit Profil & Peran

> **Kode yang diuji**: JS `openEditModal()`, `handleRoleChange()`, `UserController@update()`

### TC-E-01 — Modal Edit: Terbuka di Tengah Layar

| Atribut       | Detail                                                                                   |
| ------------- | ---------------------------------------------------------------------------------------- |
| **ID**        | TC-E-01                                                                                  |
| **Fitur**     | Modal Edit — Posisi                                                                      |
| **Teknik**    | Statement Coverage — CSS class modal                                                     |
| **Path Kode** | `index.blade.php:228–232` → `class="fixed inset-0 ... flex items-center justify-center"` |
| **Prioritas** | Tinggi                                                                                   |

**Langkah Uji**:

1. Klik tombol Aksi pada baris pengguna manapun
2. Klik "Edit Profil & Peran"
3. Amati posisi modal di layar

**Expected Result**:

- Modal muncul di **tengah layar** secara horizontal dan vertikal
- Background overlay gelap menutupi seluruh halaman
- Modal memiliki animasi scale dari 95% ke 100%

---

### TC-E-02 — Modal Edit: Data User Ter-populate dengan Benar

| Atribut       | Detail                                             |
| ------------- | -------------------------------------------------- |
| **ID**        | TC-E-02                                            |
| **Fitur**     | Modal Edit — Populasi Data                         |
| **Teknik**    | Statement Coverage → JS `openEditModal()` L600–626 |
| **Path Kode** | `index.blade.php:617–623`                          |
| **Prioritas** | Tinggi                                             |

**Langkah Uji**:

1. Klik Edit pada pengguna dengan data lengkap (nama, email, NIK, no_telp, role admin_gerai, instansi, nomor_loket)
2. Amati setiap field dalam modal

**Expected Result**:

- `f-name` = nama pengguna
- `f-email` = email pengguna
- `f-nik` = NIK pengguna
- `f-no-telp` = nomor telepon pengguna
- `f-role` = role pengguna (nilai enum string)
- `f-instansi` + `f-nomor-loket` **terisi dan terlihat** jika role = `admin_gerai`

---

### TC-E-03 — Modal Edit: Field Password Tersembunyi

| Atribut       | Detail                                                                           |
| ------------- | -------------------------------------------------------------------------------- |
| **ID**        | TC-E-03                                                                          |
| **Fitur**     | Modal Edit — Field Password                                                      |
| **Teknik**    | Branch Coverage → `isEditMode = true` → `field-password.classList.add('hidden')` |
| **Path Kode** | `index.blade.php:612`                                                            |
| **Prioritas** | Tinggi                                                                           |

**Expected Result**: Field "Password" **tidak tampil** saat modal dalam mode edit (password diubah via Reset Password terpisah)

---

### TC-E-04 — Modal Edit: Field Instansi & Loket — Muncul Saat Role admin_gerai

| Atribut       | Detail                                            |
| ------------- | ------------------------------------------------- |
| **ID**        | TC-E-04                                           |
| **Fitur**     | Modal — handleRoleChange() — Branch admin_gerai   |
| **Teknik**    | Branch Coverage → `role === 'admin_gerai'` = TRUE |
| **Path Kode** | `index.blade.php:668–671`                         |
| **Prioritas** | Tinggi                                            |

**Langkah Uji**:

1. Buka modal tambah/edit
2. Pilih role "Operator Loket" pada dropdown Role
3. Amati form

**Expected Result**:

- Section instansi & loket **muncul** (animasi `remove('hidden')`)
- Field `f-instansi` dan `f-nomor-loket` menjadi `required`

---

### TC-E-05 — Modal Edit: Field Instansi & Loket — Tersembunyi Saat Role Lain

| Atribut       | Detail                                              |
| ------------- | --------------------------------------------------- |
| **ID**        | TC-E-05                                             |
| **Fitur**     | Modal — handleRoleChange() — Branch NON admin_gerai |
| **Teknik**    | Branch Coverage → `role === 'admin_gerai'` = FALSE  |
| **Path Kode** | `index.blade.php:672–676`                           |
| **Prioritas** | Tinggi                                              |

**Langkah Uji**:

1. Buka modal, pilih role selain "Operator Loket" (mis. Super Admin, Admin FO, Pengunjung)
2. Amati form

**Expected Result**:

- Section instansi & loket **tersembunyi** (`add('hidden')`)
- Field tidak `required`

---

### TC-E-06 — Modal Edit: Simpan Perubahan Berhasil

| Atribut       | Detail                                               |
| ------------- | ---------------------------------------------------- |
| **ID**        | TC-E-06                                              |
| **Fitur**     | Edit Pengguna — Submit Form                          |
| **Teknik**    | Path Coverage → `UserController@update()` happy path |
| **Path Kode** | `UserController.php:127–153`                         |
| **Prioritas** | Tinggi                                               |

**Langkah Uji**:

1. Edit nama pengguna dari "Budi Santoso" menjadi "Budi Santoso, S.E."
2. Klik "Simpan Perubahan"
3. Amati tabel dan flash message

**Expected Result**:

- Redirect ke `/manajemen-pengguna`
- Flash success: "Data pengguna Budi Santoso, S.E. berhasil diperbarui."
- Baris tabel menampilkan nama baru

---

### TC-E-07 — Modal Edit: Validasi — Nama Wajib Diisi

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-E-07                                                         |
| **Fitur**     | Edit Pengguna — Validasi Backend                                |
| **Teknik**    | Branch Coverage → validation rule `'name' => ['required', ...]` |
| **Path Kode** | `UserController.php:128`                                        |
| **Prioritas** | Tinggi                                                          |

**Langkah Uji**:

1. Buka modal edit, kosongkan field Nama Lengkap
2. Klik "Simpan Perubahan"

**Expected Result**:

- Form tidak tersubmit
- Error validasi: "The name field is required." (atau terjemahan Indonesia)
- Modal tetap terbuka (auto-reopen via L574–576)

---

### TC-E-08 — Modal Edit: Validasi — Email Unik

| Atribut       | Detail                                                                |
| ------------- | --------------------------------------------------------------------- |
| **ID**        | TC-E-08                                                               |
| **Fitur**     | Edit Pengguna — Validasi Email Unik                                   |
| **Teknik**    | Branch Coverage → `Rule::unique('users', 'email')->ignore($user->id)` |
| **Path Kode** | `UserController.php:130`                                              |
| **Prioritas** | Tinggi                                                                |

**Langkah Uji**:

1. Edit email pengguna A menjadi email yang **sudah dipakai** pengguna B
2. Submit form

**Expected Result**: Error validasi muncul bahwa email sudah terdaftar

---

### TC-E-09 — Modal Edit: Validasi — NIK 16 Digit

| Atribut       | Detail                        |
| ------------- | ----------------------------- |
| **ID**        | TC-E-09                       |
| **Fitur**     | Edit Pengguna — Validasi NIK  |
| **Teknik**    | Branch Coverage → `'size:16'` |
| **Path Kode** | `UserController.php:129`      |
| **Prioritas** | Sedang                        |

**Langkah Uji**:

1. Isi NIK dengan 15 digit (kurang satu)
2. Submit form

**Expected Result**: Error validasi NIK harus 16 karakter

---

### TC-E-10 — Modal Edit: Validasi — Instansi & Loket Wajib Jika admin_gerai

| Atribut       | Detail                                                                               |
| ------------- | ------------------------------------------------------------------------------------ |
| **ID**        | TC-E-10                                                                              |
| **Fitur**     | Edit Pengguna — Validasi Conditional                                                 |
| **Teknik**    | Branch Coverage → `Rule::requiredIf($request->role === UserRole::AdminGerai->value)` |
| **Path Kode** | `UserController.php:133–134`                                                         |
| **Prioritas** | Tinggi                                                                               |

**Langkah Uji**:

1. Buka modal edit, set role ke "Operator Loket"
2. Kosongkan field Instansi dan Nomor Loket
3. Submit form

**Expected Result**: Error validasi instansi dan nomor loket wajib diisi untuk Operator Loket

---

### TC-E-11 — Audit Log: Data Perubahan Tercatat

| Atribut       | Detail                                            |
| ------------- | ------------------------------------------------- |
| **ID**        | TC-E-11                                           |
| **Fitur**     | Edit Pengguna — Audit Trail                       |
| **Teknik**    | Statement Coverage → `AuditLogger::userUpdated()` |
| **Path Kode** | `UserController.php:117–149`                      |
| **Prioritas** | Sedang                                            |

**Langkah Uji**:

1. Edit nama pengguna, simpan
2. Buka log aktivitas pengguna tersebut

**Expected Result**: Log aktivitas mencatat perubahan data sebelum dan sesudah edit

---

## Area 6 — Toggle Status Aktif/Nonaktif

> **Kode yang diuji**: `UserController@toggleStatus()` L160–171

### TC-TS-01 — Toggle Status: Aktif → Nonaktif

| Atribut       | Detail                                                                  |
| ------------- | ----------------------------------------------------------------------- |
| **ID**        | TC-TS-01                                                                |
| **Fitur**     | Toggle Status Pengguna — Nonaktifkan                                    |
| **Teknik**    | Branch Coverage → `!$user->is_active` saat is_active = true             |
| **Path Kode** | `UserController.php:164` → `update(['is_active' => !$user->is_active])` |
| **Prioritas** | Tinggi                                                                  |

**Langkah Uji**:

1. Pilih pengguna yang berstatus Aktif
2. Klik Aksi → "Nonaktifkan Akun"
3. Amati badge status di tabel

**Expected Result**:

- Badge status berubah dari **"Aktif"** menjadi **"Nonaktif"**
- Flash success: "Akun [nama] berhasil dinonaktifkan."
- Database: `is_active = 0`

---

### TC-TS-02 — Toggle Status: Nonaktif → Aktif

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-TS-02                                                     |
| **Fitur**     | Toggle Status Pengguna — Aktifkan                            |
| **Teknik**    | Branch Coverage → `!$user->is_active` saat is_active = false |
| **Path Kode** | `UserController.php:164`                                     |
| **Prioritas** | Tinggi                                                       |

**Langkah Uji**:

1. Pilih pengguna yang berstatus Nonaktif
2. Klik Aksi → "Aktifkan Akun"
3. Amati badge status

**Expected Result**:

- Badge status berubah menjadi **"Aktif"**
- Flash success: "Akun [nama] berhasil diaktifkan."

---

### TC-TS-03 — Toggle Status: Label Dropdown Sesuai Status

| Atribut       | Detail                                                         |
| ------------- | -------------------------------------------------------------- |
| **ID**        | TC-TS-03                                                       |
| **Fitur**     | Toggle Status — Label Dinamis di Dropdown                      |
| **Teknik**    | Branch Coverage → `@if($user->is_active)` di `table.blade.php` |
| **Path Kode** | `table.blade.php:129–135`                                      |
| **Prioritas** | Sedang                                                         |

**Expected Result**:

- Jika `is_active = true`: Dropdown menampilkan "Nonaktifkan Akun"
- Jika `is_active = false`: Dropdown menampilkan "Aktifkan Akun"

---

## Area 7 — Reset Password

> **Kode yang diuji**: `UserController@resetPassword()` L178–193

### TC-RP-01 — Reset Password: Password Sementara Dihasilkan

| Atribut       | Detail                       |
| ------------- | ---------------------------- |
| **ID**        | TC-RP-01                     |
| **Fitur**     | Reset Password               |
| **Teknik**    | Statement Coverage           |
| **Path Kode** | `UserController.php:183–192` |
| **Prioritas** | Tinggi                       |

**Langkah Uji**:

1. Klik Aksi → "Reset Password" pada salah satu pengguna
2. Konfirmasi dialog
3. Amati flash message yang muncul

**Expected Result**:

- Flash amber muncul dengan password sementara 12 karakter
- Tombol "Salin" tersedia
- Teks peringatan: "Password ini hanya ditampilkan sekali."

---

### TC-RP-02 — Reset Password: Password Lama Tidak Berlaku Lagi

| Atribut       | Detail                                           |
| ------------- | ------------------------------------------------ |
| **ID**        | TC-RP-02                                         |
| **Fitur**     | Reset Password — Validasi Password Baru          |
| **Teknik**    | Statement Coverage → `Hash::make($tempPassword)` |
| **Path Kode** | `UserController.php:185`                         |
| **Prioritas** | Tinggi                                           |

**Langkah Uji**:

1. Reset password pengguna X, catat password lama
2. Coba login sebagai pengguna X dengan password lama
3. Coba login dengan password sementara baru

**Expected Result**:

- Login dengan password lama **GAGAL**
- Login dengan password sementara **BERHASIL**

---

### TC-RP-03 — Reset Password: Konfirmasi Dialog Muncul

| Atribut       | Detail                                                |
| ------------- | ----------------------------------------------------- |
| **ID**        | TC-RP-03                                              |
| **Fitur**     | Reset Password — Konfirmasi Browser                   |
| **Teknik**    | Statement Coverage → `onsubmit="return confirm(...)"` |
| **Path Kode** | `table.blade.php:140`                                 |
| **Prioritas** | Sedang                                                |

**Langkah Uji**:

1. Klik Aksi → "Reset Password"
2. Amati apakah dialog konfirmasi muncul

**Expected Result**: Browser native `confirm()` dialog muncul sebelum request dikirim

---

## Area 8 — Hapus Pengguna

> **Kode yang diuji**: `UserController@destroy()` L200–212

### TC-D-01 — Hapus Pengguna: Berhasil

| Atribut       | Detail                       |
| ------------- | ---------------------------- |
| **ID**        | TC-D-01                      |
| **Fitur**     | Hapus Pengguna               |
| **Teknik**    | Statement Coverage           |
| **Path Kode** | `UserController.php:200–212` |
| **Prioritas** | Tinggi                       |

**Langkah Uji**:

1. Klik Aksi → "Hapus Pengguna" pada pengguna tertentu
2. Konfirmasi dialog
3. Amati tabel dan card Total Pengguna

**Expected Result**:

- Pengguna tidak lagi muncul di tabel
- Flash success: "Pengguna [nama] berhasil dihapus dari sistem."
- Card Total Pengguna berkurang 1

---

### TC-D-02 — Hapus Pengguna: Diri Sendiri Tidak Bisa Dihapus

| Atribut       | Detail                                                      |
| ------------- | ----------------------------------------------------------- |
| **ID**        | TC-D-02                                                     |
| **Fitur**     | Hapus Pengguna — Self-protection                            |
| **Teknik**    | Branch Coverage → `@if($user->id !== auth()->id())` = FALSE |
| **Path Kode** | `table.blade.php:168–177` + `UserPolicy@delete`             |
| **Prioritas** | Kritis                                                      |

**Langkah Uji**:

1. Login sebagai Super Admin
2. Buka halaman Manajemen Pengguna
3. Temukan baris akun sendiri dalam tabel

**Expected Result**:

- Tombol "Hapus Pengguna" **tidak muncul** pada baris akun sendiri (dikondisikan di Blade)

---

### TC-D-03 — Hapus Pengguna: Log Audit Dicatat Sebelum Hapus

| Atribut       | Detail                                                                    |
| ------------- | ------------------------------------------------------------------------- |
| **ID**        | TC-D-03                                                                   |
| **Fitur**     | Hapus Pengguna — Audit Trail                                              |
| **Teknik**    | Statement Coverage → `AuditLogger::userDeleted($user)` sebelum `delete()` |
| **Path Kode** | `UserController.php:205–208`                                              |
| **Prioritas** | Sedang                                                                    |

**Expected Result**: Log aktivitas penghapusan tersimpan di database meski record user sudah terhapus

---

## Area 9 — Tambah Pengguna

> **Kode yang diuji**: `UserController@store()` L83–105

### TC-A-01 — Tambah Pengguna: Berhasil dengan Data Lengkap

| Atribut       | Detail                                          |
| ------------- | ----------------------------------------------- |
| **ID**        | TC-A-01                                         |
| **Fitur**     | Tambah Pengguna — Happy Path                    |
| **Teknik**    | Path Coverage — seluruh branch validasi = valid |
| **Path Kode** | `UserController.php:87–104`                     |
| **Prioritas** | Tinggi                                          |

**Data Uji**:
| Field | Nilai |
|-------|-------|
| Nama | "Ahmad Fauzi, S.T." |
| Email | "ahmad@disdukcapil.go.id" |
| NIK | "1372010101010001" |
| No Telp | "081234567890" |
| Role | admin_gerai |
| Instansi | Disdukcapil |
| Nomor Loket | "L3" |
| Password | "Password123" |

**Expected Result**:

- Redirect ke `/manajemen-pengguna`
- Flash success: "Pengguna Ahmad Fauzi, S.T. berhasil ditambahkan."
- Pengguna baru muncul di tabel
- `is_active = true` secara default (L98)

---

### TC-A-02 — Tambah Pengguna: Validasi — Password Minimum 8 Karakter + Mixed Case + Angka

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-A-02                                                      |
| **Fitur**     | Tambah Pengguna — Validasi Password                          |
| **Teknik**    | Branch Coverage → `Password::min(8)->mixedCase()->numbers()` |
| **Path Kode** | `UserController.php:95`                                      |
| **Prioritas** | Tinggi                                                       |

**Test Data (Negatif)**:
| Skenario | Password | Expected Error |
|----------|----------|----------------|
| Terlalu pendek | `Pass1` | Minimal 8 karakter |
| Tanpa huruf besar | `password1` | Harus mengandung huruf besar & kecil |
| Tanpa angka | `Password` | Harus mengandung angka |

---

### TC-A-03 — Tambah Pengguna: is_active Default true

| Atribut       | Detail                                                |
| ------------- | ----------------------------------------------------- |
| **ID**        | TC-A-03                                               |
| **Fitur**     | Tambah Pengguna — Default Status                      |
| **Teknik**    | Statement Coverage → `$validated['is_active'] = true` |
| **Path Kode** | `UserController.php:98`                               |
| **Prioritas** | Sedang                                                |

**Langkah Uji**:

1. Tambah pengguna baru (tidak ada field status di form)
2. Amati status pengguna di tabel

**Expected Result**: Pengguna baru langsung berstatus **Aktif** tanpa perlu mengaktifkan manual

---

## Area 10 — Keamanan & Otorisasi

### TC-SEC-01 — Akses Halaman: Hanya Super Admin

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-SEC-01                                                    |
| **Fitur**     | Otorisasi — Gate Policy                                      |
| **Teknik**    | Branch Coverage → `$this->authorize('viewAny', User::class)` |
| **Path Kode** | `UserController.php:24`                                      |
| **Prioritas** | Kritis                                                       |

**Langkah Uji**:

1. Login sebagai role selain `super_admin` (mis. `admin_fo`, `admin_gerai`, `pengunjung`)
2. Akses langsung URL `/manajemen-pengguna`

**Expected Result**: Redirect ke halaman 403 Forbidden atau dashboard, bukan halaman manajemen pengguna

---

### TC-SEC-02 — Toggle Status: Tidak Bisa Nonaktifkan Diri Sendiri

| Atribut       | Detail                                                               |
| ------------- | -------------------------------------------------------------------- |
| **ID**        | TC-SEC-02                                                            |
| **Fitur**     | Toggle Status — Self-protection via Policy                           |
| **Teknik**    | Branch Coverage → `UserPolicy@toggleStatus`                          |
| **Path Kode** | `UserController.php:162` → `$this->authorize('toggleStatus', $user)` |
| **Prioritas** | Kritis                                                               |

**Langkah Uji**:

1. Login sebagai Super Admin
2. Kirim request PATCH ke `/manajemen-pengguna/{id_sendiri}/toggle-status` langsung via Postman

**Expected Result**: HTTP 403 Forbidden

---

## Ringkasan Coverage

| Area            | Jumlah TC | Branch Covered                                                     | Priority Kritis                    |
| --------------- | --------- | ------------------------------------------------------------------ | ---------------------------------- |
| Statistik Cards | 7         | `count()`, `online()`, `distinct()`, `whereIn()`, `whereNotNull()` | TC-M-01, TC-M-04                   |
| Live Search     | 8         | `filled('search')`, AJAX branch, debounce, history.pushState       | TC-S-01, TC-S-06                   |
| Filter          | 9         | `filled()` per filter, `in_array()` enum, kombinasi                | TC-F-01, TC-F-03, TC-F-05, TC-F-07 |
| Tabel           | 10        | `@forelse/@empty`, `@if` tiap kolom, pagination                    | TC-T-01, TC-T-05, TC-T-09          |
| Modal Edit      | 11        | `isEditMode`, `handleRoleChange()`, validasi backend               | TC-E-01, TC-E-06, TC-E-10          |
| Toggle Status   | 3         | `!is_active` boolean flip                                          | TC-TS-01, TC-TS-02                 |
| Reset Password  | 3         | `Hash::make`, flash session                                        | TC-RP-01, TC-RP-02                 |
| Hapus           | 3         | `$user->id !== auth()->id()`, audit before delete                  | TC-D-02                            |
| Tambah          | 3         | Validasi password, `is_active` default                             | TC-A-01, TC-A-02                   |
| Keamanan        | 2         | Gate Policy `viewAny`, `toggleStatus`                              | TC-SEC-01, TC-SEC-02               |
| **TOTAL**       | **59**    |                                                                    |                                    |

---

## Template Laporan Hasil Uji

Gunakan tabel berikut saat pelaksanaan pengujian:

| ID TC     | Nama Fitur                 | Status  | Tanggal Uji | Penguji       | Keterangan                                                                                   |
| --------- | -------------------------- | ------- | ----------- | ------------- | -------------------------------------------------------------------------------------------- |
| TC-M-01   | Total Pengguna Valid       | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-M-04   | Staf Aktif Online Valid    | ❌ FAIL | 07/06/2026  | Zaky Ramadhan | Jika menggunakan query SQL yang diberikan FAIL, tapi jika simbol `>=` diganti jadi `<=` PASS |
| TC-S-01   | Search by Nama             | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-S-06   | AJAX Response JSON         | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-F-01   | Filter Instansi            | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-F-07   | Filter Kombinasi           | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-T-09   | Pagination 10 Data         | ⏭️ SKIP | 07/06/2026  | Zaky Ramadhan | Data user hanya ada 4                                                                        |
| TC-E-01   | Modal Posisi Tengah        | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-E-06   | Edit Simpan Berhasil       | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-TS-01  | Toggle Nonaktifkan         | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-RP-01  | Reset Password             | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-D-02   | Hapus Diri Sendiri Dicegah | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |
| TC-SEC-01 | Akses Hanya Super Admin    | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai, tapi perlu ada redirect otomatis ke halaman dashboard                                |
| TC-SEC-02 | Toggle Self Forbidden      | ✅ PASS | 07/06/2026  | Zaky Ramadhan | Sesuai                                                                                       |

> **Status**: ✅ PASS · ❌ FAIL · ⏭️ SKIP · ⬜ BELUM

---

_Dokumen ini dibuat berdasarkan analisis kode sumber langsung (white-box). Setiap test case mencantumkan referensi file dan nomor baris kode yang diuji._
