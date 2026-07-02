# Test Case — Halaman Konfigurasi Gerai

**Metode**: White-Box Testing  
**Halaman Uji**: `/konfigurasi-gerai-loket` (super_admin/gerai/index.blade.php)  
**Versi Aplikasi**: MPP Kota Sawahlunto  
**Tanggal Dibuat**: 2026-06-09  
**Dibuat oleh**: QA Engineer

---

> [!IMPORTANT]
> **Metode White-Box Testing** menguji logika internal kode, bukan hanya antarmuka. Setiap test case memetakan ke **path kode spesifik** di controller, model, atau JavaScript yang dieksekusi. Teknik yang digunakan: _Statement Coverage_, _Branch Coverage_, dan _Path Coverage_.

---

## Peta Komponen yang Diuji

| Komponen                          | File                                             | Baris Kunci |
| --------------------------------- | ------------------------------------------------ | ----------- |
| Controller `index()`              | `GeraiLoketController.php`                       | L21–49      |
| Controller `storeDepartment()`    | `GeraiLoketController.php`                       | L55–91      |
| Controller `updateDepartment()`   | `GeraiLoketController.php`                       | L93–140     |
| Controller `destroyDepartment()`  | `GeraiLoketController.php`                       | L142–164    |
| Model `Department`                | `Department.php`                                 | L1–52       |
| Migration `departments`           | `2026_05_25_070001_create_departments_table.php` | L14–21      |
| Policy `viewAny()`                | `UserPolicy.php`                                 | L26–29      |
| View metrics                      | `metrics.blade.php`                              | L1–31       |
| View table (desktop)              | `table.blade.php`                                | L1–54       |
| View cards (mobile)               | `cards.blade.php`                                | L1–38       |
| JavaScript `openAddGeraiModal()`  | `modal.blade.php`                                | L105–117    |
| JavaScript `openEditGeraiModal()` | `modal.blade.php`                                | L119–142    |
| JavaScript `previewLogo()`        | `modal.blade.php`                                | L65–91      |
| JavaScript `clearLogoSelection()` | `modal.blade.php`                                | L93–103     |
| JavaScript `closeGeraiModal()`    | `modal.blade.php`                                | L144–147    |

---

## Area 1 — Statistik Cards (Metrics)

> **Kode yang diuji**: `GeraiLoketController@index` L26–29 dan `metrics.blade.php`

### TC-M-01 — Total Gerai Instansi: Hitungan Valid

| Atribut       | Detail                                                   |
| ------------- | -------------------------------------------------------- |
| **ID**        | TC-M-01                                                  |
| **Fitur**     | Card Total Gerai Instansi                                |
| **Teknik**    | Statement Coverage                                       |
| **Path Kode** | `GeraiLoketController.php:26` → `Department::count('*')` |
| **Prioritas** | Tinggi                                                   |

**Precondition**:

- Login sebagai Super Admin
- Database memiliki sejumlah gerai (mis. N gerai)

**Langkah Uji**:

1. Catat jumlah baris di tabel `departments` via SQL: `SELECT COUNT(*) FROM departments;`
2. Buka halaman `/konfigurasi-gerai-loket`
3. Amati angka pada card **"Total Gerai Instansi"**

**Expected Result**:

- Angka pada card = hasil query SQL `COUNT(*)` di atas
- Tidak ada selisih satu pun

**Verification Query**:

```sql
SELECT COUNT(*) AS total FROM departments;
```

**Hasil Uji**: ⬜ BELUM

---

### TC-M-02 — Total Gerai: Angka Bertambah Setelah Tambah Gerai

| Atribut       | Detail                                               |
| ------------- | ---------------------------------------------------- |
| **ID**        | TC-M-02                                              |
| **Fitur**     | Card Total Gerai Instansi — Sinkronisasi             |
| **Teknik**    | Statement Coverage                                   |
| **Path Kode** | `Department::count()` setelah `Department::create()` |
| **Prioritas** | Tinggi                                               |

**Precondition**: N gerai ada di database

**Langkah Uji**:

1. Catat angka Total Gerai Instansi di halaman (N)
2. Tambah satu gerai baru via tombol **"Tambah Gerai"**
3. Reload halaman `/konfigurasi-gerai-loket`
4. Amati angka card Total Gerai Instansi

**Expected Result**: Angka berubah menjadi N+1

**Hasil Uji**: ⬜ BELUM

---

### TC-M-03 — Total Gerai: Angka Berkurang Setelah Hapus Gerai

| Atribut       | Detail                                                                   |
| ------------- | ------------------------------------------------------------------------ |
| **ID**        | TC-M-03                                                                  |
| **Fitur**     | Card Total Gerai Instansi — Sinkronisasi                                 |
| **Teknik**    | Branch Coverage                                                          |
| **Path Kode** | `GeraiLoketController@destroyDepartment` → `delete()` → reload `count()` |
| **Prioritas** | Tinggi                                                                   |

**Precondition**: N gerai ada di database

**Langkah Uji**:

1. Catat angka Total Gerai Instansi (N)
2. Hapus satu gerai via tombol Hapus → konfirmasi dialog
3. Reload halaman
4. Amati angka card Total Gerai Instansi

**Expected Result**: Angka berubah menjadi N−1

**Hasil Uji**: ⬜ BELUM

---

### TC-M-04 — Total Petugas: Hitungan Valid (Hanya role admin_gerai)

| Atribut       | Detail                                                                           |
| ------------- | -------------------------------------------------------------------------------- |
| **ID**        | TC-M-04                                                                          |
| **Fitur**     | Card Total Petugas                                                               |
| **Teknik**    | Statement + Branch Coverage                                                      |
| **Path Kode** | `GeraiLoketController.php:27–29` → `User::where('role', AdminGerai)->count('*')` |
| **Prioritas** | Tinggi                                                                           |

**Precondition**: Ada beberapa user dengan role `admin_gerai` dan role lain di database

**Langkah Uji**:

1. Hitung via SQL:
    ```sql
    SELECT COUNT(*) FROM users WHERE role = 'admin_gerai';
    ```
2. Buka halaman `/konfigurasi-gerai-loket`
3. Bandingkan angka card **"Total Petugas"** dengan hasil SQL

**Expected Result**: Angka card = hasil query SQL

**Catatan White-Box**:

- `UserRole::AdminGerai->value` → string `'admin_gerai'` — dikonfirmasi di `GeraiLoketController.php:28`
- Pengguna dengan role `super_admin`, `admin_fo`, dan `pengunjung` **tidak** dihitung

**Hasil Uji**: ⬜ BELUM

---

### TC-M-05 — Total Petugas: Role Lain Tidak Dihitung

| Atribut       | Detail                                                    |
| ------------- | --------------------------------------------------------- |
| **ID**        | TC-M-05                                                   |
| **Fitur**     | Card Total Petugas — Validasi Eksklusif Role              |
| **Teknik**    | Branch Coverage (sisi FALSE dari `where('role', ...)`)    |
| **Path Kode** | `GeraiLoketController.php:28` — kondisi `where` role enum |
| **Prioritas** | Sedang                                                    |

**Precondition**: Ada user `super_admin` dan `admin_fo` di database

**Langkah Uji**:

1. Catat angka Total Petugas di halaman
2. Tambah satu user baru dengan role `super_admin` (bukan `admin_gerai`)
3. Reload halaman dan periksa card Total Petugas

**Expected Result**: Angka card tidak bertambah karena role bukan `admin_gerai`

**Hasil Uji**: ⬜ BELUM

---

## Area 2 — Tabel Gerai (Desktop)

> **Kode yang diuji**: `table.blade.php` seluruhnya + `GeraiLoketController@index` L32

### TC-T-01 — Tabel: Kolom Nama & Kode Prefix Tampil

| Atribut       | Detail                   |
| ------------- | ------------------------ |
| **ID**        | TC-T-01                  |
| **Fitur**     | Tabel — Kolom Data Gerai |
| **Teknik**    | Statement Coverage       |
| **Path Kode** | `table.blade.php:26–31`  |
| **Prioritas** | Tinggi                   |

**Langkah Uji**:

1. Buka halaman `/konfigurasi-gerai-loket` (tampilan desktop ≥768px)
2. Amati setiap baris tabel
3. Periksa apakah nama dan kode prefix ditampilkan sesuai data di database

**Expected Result**:

- Nama gerai ditampilkan via `{{ $dept->name }}`
- Kode prefix ditampilkan via `{{ $dept->inisial }}` dengan badge styling monospace
- Deskripsi singkat ditampilkan via `{{ $dept->description ?? '-' }}`

**Verification Query**:

```sql
SELECT name, inisial, description FROM departments ORDER BY created_at DESC;
```

**Hasil Uji**: ⬜ BELUM

---

### TC-T-02 — Tabel: Logo Ada → Tampilkan Gambar

| Atribut       | Detail                                      |
| ------------- | ------------------------------------------- |
| **ID**        | TC-T-02                                     |
| **Fitur**     | Tabel — Kolom Logo (Branch: logo tersedia)  |
| **Teknik**    | Branch Coverage → `@if($dept->logo)` = TRUE |
| **Path Kode** | `table.blade.php:17–18`                     |
| **Prioritas** | Sedang                                      |

**Precondition**: Ada gerai yang sudah memiliki file logo di `storage/logos/`

**Langkah Uji**:

1. Buka halaman
2. Lihat baris gerai yang memiliki logo

**Expected Result**:

- Elemen `<img>` ditampilkan dengan `src` mengarah ke `Storage::disk('public')->url($dept->logo)`
- Gambar terbaca dan tidak broken
- Dimensi thumbnail 40×40px (class `w-10 h-10`)

**Hasil Uji**: ⬜ BELUM

---

### TC-T-03 — Tabel: Logo Tidak Ada → Tampilkan Placeholder Inisial

| Atribut       | Detail                                       |
| ------------- | -------------------------------------------- |
| **ID**        | TC-T-03                                      |
| **Fitur**     | Tabel — Kolom Logo (Branch: logo kosong)     |
| **Teknik**    | Branch Coverage → `@if($dept->logo)` = FALSE |
| **Path Kode** | `table.blade.php:19–24`                      |
| **Prioritas** | Sedang                                       |

**Precondition**: Ada gerai yang tidak memiliki logo (`logo = NULL`)

**Langkah Uji**:

1. Buka halaman
2. Lihat baris gerai tanpa logo

**Expected Result**:

- Div placeholder ditampilkan (bukan `<img>`)
- Berisi 2 karakter pertama nama gerai: `{{ substr($dept->name, 0, 2) }}`
- Styling: background primary/10, text primary

**Catatan White-Box**: Ada potensi bug di `table.blade.php:22` — `{{$dept->logo}}` tercetak di dalam div placeholder meski bernilai NULL. Perlu diinvestigasi.

**Hasil Uji**: ⬜ BELUM

---

### TC-T-04 — Tabel: Deskripsi Kosong → Tampilkan Dash

| Atribut       | Detail                                        |
| ------------- | --------------------------------------------- |
| **ID**        | TC-T-04                                       |
| **Fitur**     | Tabel — Kolom Deskripsi (Branch: data kosong) |
| **Teknik**    | Branch Coverage → null coalescing `?? '-'`    |
| **Path Kode** | `table.blade.php:33`                          |
| **Prioritas** | Rendah                                        |

**Langkah Uji**:

1. Buat atau temukan gerai tanpa deskripsi (`description = NULL`)
2. Lihat kolom Deskripsi pada baris tersebut

**Expected Result**: Kolom Deskripsi menampilkan tanda **"—"** (dash)

**Hasil Uji**: ⬜ BELUM

---

### TC-T-05 — Tabel: Empty State Saat Tidak Ada Gerai

| Atribut       | Detail                                         |
| ------------- | ---------------------------------------------- |
| **ID**        | TC-T-05                                        |
| **Fitur**     | Tabel — Empty State                            |
| **Teknik**    | Branch Coverage → `@forelse` → `@empty` branch |
| **Path Kode** | `table.blade.php:44–50`                        |
| **Prioritas** | Tinggi                                         |

**Precondition**: Tabel `departments` kosong (tidak ada data)

**Langkah Uji**:

1. Hapus semua gerai dari database
2. Buka halaman `/konfigurasi-gerai-loket`
3. Amati area tabel

**Expected Result**:

- Tidak ada baris data yang ditampilkan
- Pesan empty state muncul: _"Belum ada data Gerai terdaftar. Klik "Tambah Gerai" untuk menambahkan."_
- Pesan ditampilkan di dalam `<td colspan="7">`

**Hasil Uji**: ⬜ BELUM

---

### TC-T-06 — Tabel: Data Diurutkan Terbaru (latest)

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-T-06                                                         |
| **Fitur**     | Tabel — Urutan Data                                             |
| **Teknik**    | Statement Coverage → `Department::withCount()->latest()->get()` |
| **Path Kode** | `GeraiLoketController.php:32`                                   |
| **Prioritas** | Sedang                                                          |

**Langkah Uji**:

1. Catat urutan gerai di database berdasarkan `created_at` DESC
2. Buka halaman dan amati urutan baris tabel

**Expected Result**: Baris pertama tabel = gerai yang paling baru dibuat (created_at terbesar)

**Verification Query**:

```sql
SELECT name, created_at FROM departments ORDER BY created_at DESC;
```

**Hasil Uji**: ⬜ BELUM

---

## Area 3 — Card Gerai (Mobile)

> **Kode yang diuji**: `cards.blade.php` seluruhnya

### TC-C-01 — Cards: Tampil pada Layar Mobile

| Atribut       | Detail                                                           |
| ------------- | ---------------------------------------------------------------- |
| **ID**        | TC-C-01                                                          |
| **Fitur**     | Mobile Cards Layout                                              |
| **Teknik**    | Statement Coverage → CSS responsive `md:hidden`                  |
| **Path Kode** | `cards.blade.php:2` — `class="grid grid-cols-1 gap-4 md:hidden"` |
| **Prioritas** | Sedang                                                           |

**Langkah Uji**:

1. Buka halaman di browser mobile (lebar < 768px) atau resize browser ke lebar < 768px
2. Amati layout yang ditampilkan

**Expected Result**:

- Layout kartu tampil (bukan tabel)
- Tabel desktop **tersembunyi** (class `hidden md:block` di `table.blade.php:2`)
- Setiap gerai ditampilkan sebagai kartu terpisah

**Hasil Uji**: ⬜ BELUM

---

### TC-C-02 — Cards: Logo & Inisial pada Mobile

| Atribut       | Detail                                            |
| ------------- | ------------------------------------------------- |
| **ID**        | TC-C-02                                           |
| **Fitur**     | Mobile Cards — Logo Branch                        |
| **Teknik**    | Branch Coverage → `@if($dept->logo)` = TRUE/FALSE |
| **Path Kode** | `cards.blade.php:6–12`                            |
| **Prioritas** | Rendah                                            |

**Expected Result**:

- Gerai dengan logo → `<img>` 48×48px ditampilkan
- Gerai tanpa logo → div placeholder dengan 2 karakter pertama nama

**Hasil Uji**: ⬜ BELUM

---

### TC-C-03 — Cards: Deskripsi Hanya Tampil Jika Ada

| Atribut       | Detail                                                   |
| ------------- | -------------------------------------------------------- |
| **ID**        | TC-C-03                                                  |
| **Fitur**     | Mobile Cards — Deskripsi Conditional                     |
| **Teknik**    | Branch Coverage → `@if($dept->description)` = TRUE/FALSE |
| **Path Kode** | `cards.blade.php:18–22`                                  |
| **Prioritas** | Rendah                                                   |

**Expected Result**:

- Gerai **dengan** deskripsi → paragraf deskripsi tampil (max 2 baris, class `line-clamp-2`)
- Gerai **tanpa** deskripsi → paragraf tidak dirender sama sekali

**Hasil Uji**: ⬜ BELUM

---

## Area 4 — Modal Tambah Gerai

> **Kode yang diuji**: JS `openAddGeraiModal()` + `GeraiLoketController@storeDepartment()`

### TC-AG-01 — Modal Tambah: Terbuka dengan Form Kosong

| Atribut       | Detail                                        |
| ------------- | --------------------------------------------- |
| **ID**        | TC-AG-01                                      |
| **Fitur**     | Modal Tambah Gerai — State Awal               |
| **Teknik**    | Statement Coverage → JS `openAddGeraiModal()` |
| **Path Kode** | `modal.blade.php:105–117`                     |
| **Prioritas** | Tinggi                                        |

**Langkah Uji**:

1. Klik tombol **"Tambah Gerai"** di halaman
2. Amati modal yang muncul

**Expected Result**:

- Modal muncul (`#gerai-modal` class `hidden` dihapus)
- Judul modal = **"Tambah Gerai Instansi"**
- Field `#g-name` kosong (`value = ''`)
- Field `#g-inisial` kosong
- Field `#g-desc` kosong
- Preview logo tersembunyi (`#logo-preview-container` memiliki class `hidden`)
- `action` form = route `config.departments.store` (POST)
- `#gerai-form-method` value = `'POST'`

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-02 — Tambah Gerai: Berhasil dengan Data Lengkap

| Atribut       | Detail                                          |
| ------------- | ----------------------------------------------- |
| **ID**        | TC-AG-02                                        |
| **Fitur**     | Tambah Gerai — Happy Path                       |
| **Teknik**    | Path Coverage — seluruh branch validasi = valid |
| **Path Kode** | `GeraiLoketController.php:59–91`                |
| **Prioritas** | Tinggi                                          |

**Data Uji**:

| Field       | Nilai                                       |
| ----------- | ------------------------------------------- |
| Nama        | "Dinas Kependudukan dan Catatan Sipil"      |
| Kode Prefix | "DDK"                                       |
| Logo        | _(file gambar valid, ≤ 2MB)_                |
| Deskripsi   | "Melayani administrasi kependudukan warga." |

**Langkah Uji**:

1. Klik "Tambah Gerai"
2. Isi semua field sesuai data uji
3. Upload file logo
4. Klik "Simpan"

**Expected Result**:

- Redirect ke `/konfigurasi-gerai-loket?tab=gerai`
- Flash success: `"Gerai Dinas Kependudukan dan Catatan Sipil berhasil dibuat."`
- Gerai baru muncul di baris pertama tabel (karena `latest()`)
- Audit log event `department_created` tercatat di database

**Verification Query**:

```sql
SELECT * FROM departments WHERE name = 'Dinas Kependudukan dan Catatan Sipil';
```

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-03 — Tambah Gerai: Berhasil Tanpa Logo & Deskripsi (Field Opsional)

| Atribut       | Detail                                                |
| ------------- | ----------------------------------------------------- |
| **ID**        | TC-AG-03                                              |
| **Fitur**     | Tambah Gerai — Field Opsional Nullable                |
| **Teknik**    | Branch Coverage → `$request->hasFile('logo')` = FALSE |
| **Path Kode** | `GeraiLoketController.php:66` — kondisi FALSE         |
| **Prioritas** | Tinggi                                                |

**Data Uji**:

| Field       | Nilai      |
| ----------- | ---------- |
| Nama        | "Dispora"  |
| Kode Prefix | "DSP"      |
| Logo        | _(kosong)_ |
| Deskripsi   | _(kosong)_ |

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Isi hanya Nama dan Kode Prefix
3. Klik "Simpan"

**Expected Result**:

- Redirect sukses dengan flash success
- `logo = NULL` di database
- `description = NULL` di database
- Kolom logo di tabel menampilkan placeholder inisial (bukan gambar)

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-04 — Tambah Gerai: Validasi — Nama Wajib Diisi

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-AG-04                                                        |
| **Fitur**     | Tambah Gerai — Validasi Backend: Nama                           |
| **Teknik**    | Branch Coverage → validation rule `'name' => ['required', ...]` |
| **Path Kode** | `GeraiLoketController.php:60`                                   |
| **Prioritas** | Tinggi                                                          |

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Biarkan field "Nama Instansi" kosong
3. Isi field lain dengan data valid
4. Klik "Simpan"

**Expected Result**:

- Form tidak tersubmit ke server (HTML5 `required` attribute mencegah)
- **Atau** jika form mencapai server: error validasi `'name' => 'required'` dikembalikan
- Modal tetap terbuka (atau redirect balik dengan error)

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-05 — Tambah Gerai: Validasi — Kode Prefix Wajib Diisi

| Atribut       | Detail                                                               |
| ------------- | -------------------------------------------------------------------- |
| **ID**        | TC-AG-05                                                             |
| **Fitur**     | Tambah Gerai — Validasi Backend: Inisial Required                    |
| **Teknik**    | Branch Coverage → `'inisial' => ['required', 'max:6', 'unique:...']` |
| **Path Kode** | `GeraiLoketController.php:61`                                        |
| **Prioritas** | Tinggi                                                               |

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Isi Nama Instansi tapi kosongkan field "Kode Prefix Antrean"
3. Klik "Simpan"

**Expected Result**:

- Error validasi: field kode prefix wajib diisi
- Gerai tidak tersimpan di database

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-06 — Tambah Gerai: Validasi — Kode Prefix Unik

| Atribut       | Detail                                                 |
| ------------- | ------------------------------------------------------ |
| **ID**        | TC-AG-06                                               |
| **Fitur**     | Tambah Gerai — Validasi Unique Inisial                 |
| **Teknik**    | Branch Coverage → `'unique:departments,inisial'` gagal |
| **Path Kode** | `GeraiLoketController.php:61`                          |
| **Prioritas** | Tinggi                                                 |

**Precondition**: Ada gerai dengan inisial "DDK" di database

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Isi Kode Prefix dengan "DDK" (yang sudah ada)
3. Klik "Simpan"

**Expected Result**:

- Error validasi: _"The inisial has already been taken."_ (atau terjemahan Indonesia)
- Gerai baru tidak tersimpan
- Inisial yang sudah ada tidak terduplikasi

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-07 — Tambah Gerai: Validasi — Kode Prefix Maksimal 6 Karakter

| Atribut       | Detail                                     |
| ------------- | ------------------------------------------ |
| **ID**        | TC-AG-07                                   |
| **Fitur**     | Tambah Gerai — Validasi Max Length Inisial |
| **Teknik**    | Branch Coverage → `'max:6'` gagal          |
| **Path Kode** | `GeraiLoketController.php:61`              |
| **Prioritas** | Sedang                                     |

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Isi Kode Prefix dengan 7 karakter atau lebih, mis. "ABCDEFG"
3. Klik "Simpan"

**Expected Result**:

- HTML5 `maxlength="6"` pada field `#g-inisial` mencegah input lebih dari 6 karakter di frontend
- Jika bypass via Postman: error validasi `max:6`

**Catatan White-Box**: Atribut `maxlength="6"` ada di `modal.blade.php:29` sebagai validasi frontend. Backend tetap validasi di L61.

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-08 — Tambah Gerai: Validasi — Logo Format & Ukuran

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-AG-08                                                        |
| **Fitur**     | Tambah Gerai — Validasi Logo Upload                             |
| **Teknik**    | Branch Coverage → `'logo' => ['nullable', 'image', 'max:2048']` |
| **Path Kode** | `GeraiLoketController.php:62`                                   |
| **Prioritas** | Tinggi                                                          |

**Test Data (Negatif)**:

| Skenario      | File           | Expected Error           |
| ------------- | -------------- | ------------------------ |
| Bukan gambar  | `document.pdf` | Harus berupa file gambar |
| Terlalu besar | gambar > 2MB   | Maks ukuran 2048KB (2MB) |

**Langkah Uji (per skenario)**:

1. Buka modal Tambah Gerai
2. Upload file sesuai skenario
3. Klik "Simpan"

**Expected Result**:

- Validasi JS `previewLogo()` di `modal.blade.php:74–78`: alert browser muncul jika ukuran > 2MB sebelum submit
- Backend: jika bypass frontend, error validasi Laravel dikembalikan

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-09 — Tambah Gerai: Logo Dikonversi ke WebP

| Atribut       | Detail                                                           |
| ------------- | ---------------------------------------------------------------- |
| **ID**        | TC-AG-09                                                         |
| **Fitur**     | Tambah Gerai — Konversi Logo ke WebP                             |
| **Teknik**    | Statement Coverage → `ImageManager` + `WebpEncoder(quality: 80)` |
| **Path Kode** | `GeraiLoketController.php:71–75`                                 |
| **Prioritas** | Sedang                                                           |

**Langkah Uji**:

1. Upload logo berformat PNG atau JPG
2. Klik "Simpan"
3. Periksa file yang tersimpan di `storage/app/public/logos/`

**Expected Result**:

- File tersimpan dengan ekstensi `.webp` (bukan `.png`/`.jpg`)
- Path dimulai dengan `logos/` diikuti nama acak hex 40 karakter
- Kolom `logo` di database berisi path relatif, mis. `logos/abc123...def.webp`

**Verification Query**:

```sql
SELECT logo FROM departments ORDER BY created_at DESC LIMIT 1;
```

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-10 — Preview Logo: Tampil Saat File Dipilih

| Atribut       | Detail                                                   |
| ------------- | -------------------------------------------------------- |
| **ID**        | TC-AG-10                                                 |
| **Fitur**     | Modal — Preview Logo JavaScript                          |
| **Teknik**    | Branch Coverage → `input.files && input.files[0]` = TRUE |
| **Path Kode** | `modal.blade.php:70–90`                                  |
| **Prioritas** | Sedang                                                   |

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Klik input file dan pilih file gambar valid (< 2MB)
3. Amati area preview

**Expected Result**:

- Container preview `#logo-preview-container` muncul (class `hidden` dihapus)
- `<img#logo-preview>` menampilkan pratinjau gambar yang dipilih (via `FileReader.readAsDataURL`)
- `#logo-preview-name` menampilkan nama file
- Tombol "Hapus Pilihan" tersedia

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-11 — Preview Logo: Alert Muncul Jika Ukuran > 2MB

| Atribut       | Detail                                                 |
| ------------- | ------------------------------------------------------ |
| **ID**        | TC-AG-11                                               |
| **Fitur**     | Modal — Validasi Ukuran Logo (Frontend JS)             |
| **Teknik**    | Branch Coverage → `file.size > 2 * 1024 * 1024` = TRUE |
| **Path Kode** | `modal.blade.php:74–79`                                |
| **Prioritas** | Tinggi                                                 |

**Langkah Uji**:

1. Buka modal Tambah Gerai
2. Pilih file gambar berukuran > 2MB
3. Amati perilaku

**Expected Result**:

- Alert browser muncul: `"Ukuran file melebihi batas maksimum 2MB."`
- Input file dikosongkan (`input.value = ''`)
- Preview tetap tersembunyi (`clearLogoSelection()` dipanggil)

**Hasil Uji**: ⬜ BELUM

---

### TC-AG-12 — Hapus Pilihan Logo: Preview Dibersihkan

| Atribut       | Detail                                      |
| ------------- | ------------------------------------------- |
| **ID**        | TC-AG-12                                    |
| **Fitur**     | Modal — Tombol "Hapus Pilihan" Logo         |
| **Teknik**    | Statement Coverage → `clearLogoSelection()` |
| **Path Kode** | `modal.blade.php:93–103`                    |
| **Prioritas** | Rendah                                      |

**Langkah Uji**:

1. Pilih file gambar di modal (preview muncul)
2. Klik tombol "Hapus Pilihan"

**Expected Result**:

- `input.value` dikosongkan
- `preview.src` direset ke `'#'`
- `nameLabel.innerText` dikosongkan
- Container preview tersembunyi kembali (`container.classList.add('hidden')`)

**Hasil Uji**: ⬜ BELUM

---

## Area 5 — Modal Edit Gerai

> **Kode yang diuji**: JS `openEditGeraiModal()` + `GeraiLoketController@updateDepartment()`

### TC-EG-01 — Modal Edit: Terbuka dengan Data Ter-populate

| Atribut       | Detail                                                   |
| ------------- | -------------------------------------------------------- |
| **ID**        | TC-EG-01                                                 |
| **Fitur**     | Modal Edit Gerai — Populasi Data                         |
| **Teknik**    | Statement Coverage → JS `openEditGeraiModal(department)` |
| **Path Kode** | `modal.blade.php:119–142`                                |
| **Prioritas** | Tinggi                                                   |

**Langkah Uji**:

1. Klik tombol **"Edit"** pada salah satu baris gerai di tabel
2. Amati setiap field dalam modal

**Expected Result**:

- Judul modal berubah menjadi **"Edit Gerai Instansi"**
- `#g-name` terisi = `department.name`
- `#g-inisial` terisi = `department.inisial`
- `#g-desc` terisi = `department.description` (atau kosong jika NULL)
- `action` form = `/konfigurasi-gerai-loket/departments/{id}` (PUT)
- `#gerai-form-method` value = `'PUT'`

**Hasil Uji**: ⬜ BELUM

---

### TC-EG-02 — Modal Edit: Logo Saat Ini Ditampilkan di Preview

| Atribut       | Detail                                          |
| ------------- | ----------------------------------------------- |
| **ID**        | TC-EG-02                                        |
| **Fitur**     | Modal Edit — Preview Logo Existing              |
| **Teknik**    | Branch Coverage → `if (department.logo)` = TRUE |
| **Path Kode** | `modal.blade.php:129–136`                       |
| **Prioritas** | Sedang                                          |

**Precondition**: Ada gerai dengan logo tersimpan di database

**Langkah Uji**:

1. Klik Edit pada gerai yang memiliki logo

**Expected Result**:

- Container preview muncul (class `hidden` dihapus)
- `#logo-preview` src = `/storage/{path_logo}`
- `#logo-preview-name` = `"Logo Saat Ini"`

**Hasil Uji**: ⬜ BELUM

---

### TC-EG-03 — Modal Edit: Tidak Ada Logo → Preview Tersembunyi

| Atribut       | Detail                                           |
| ------------- | ------------------------------------------------ |
| **ID**        | TC-EG-03                                         |
| **Fitur**     | Modal Edit — Preview Logo Kosong                 |
| **Teknik**    | Branch Coverage → `if (department.logo)` = FALSE |
| **Path Kode** | `modal.blade.php:137–139`                        |
| **Prioritas** | Rendah                                           |

**Precondition**: Ada gerai dengan `logo = NULL`

**Langkah Uji**:

1. Klik Edit pada gerai tanpa logo

**Expected Result**:

- `clearLogoSelection()` dipanggil
- Container preview tetap tersembunyi

**Hasil Uji**: ⬜ BELUM

---

### TC-EG-04 — Edit Gerai: Simpan Perubahan Berhasil

| Atribut       | Detail                                                    |
| ------------- | --------------------------------------------------------- |
| **ID**        | TC-EG-04                                                  |
| **Fitur**     | Edit Gerai — Submit Form Happy Path                       |
| **Teknik**    | Path Coverage → `GeraiLoketController@updateDepartment()` |
| **Path Kode** | `GeraiLoketController.php:97–139`                         |
| **Prioritas** | Tinggi                                                    |

**Langkah Uji**:

1. Klik Edit pada gerai "Dispora"
2. Ubah nama dari "Dispora" menjadi "Dinas Pemuda dan Olahraga"
3. Klik "Simpan"

**Expected Result**:

- Redirect ke `/konfigurasi-gerai-loket?tab=gerai`
- Flash success: `"Gerai Dinas Pemuda dan Olahraga berhasil diperbarui."`
- Baris tabel menampilkan nama baru
- Audit log event `department_updated` tercatat dengan data `before` dan `after`

**Verification Query**:

```sql
SELECT name, inisial FROM departments WHERE id = {id_gerai};
```

**Hasil Uji**: ⬜ BELUM

---

### TC-EG-05 — Edit Gerai: Logo Lama Dihapus Saat Upload Logo Baru

| Atribut       | Detail                                                         |
| ------------- | -------------------------------------------------------------- |
| **ID**        | TC-EG-05                                                       |
| **Fitur**     | Edit Gerai — Penghapusan Logo Lama                             |
| **Teknik**    | Branch Coverage → `if ($department->logo)` sebelum upload baru |
| **Path Kode** | `GeraiLoketController.php:108–110`                             |
| **Prioritas** | Sedang                                                         |

**Precondition**: Ada gerai dengan logo yang tersimpan

**Langkah Uji**:

1. Catat nama file logo lama dari database
2. Klik Edit, upload logo baru
3. Klik "Simpan"
4. Periksa storage: apakah file logo lama sudah terhapus

**Expected Result**:

- File logo lama **dihapus** dari `storage/app/public/logos/`
- File logo baru tersimpan (dalam format `.webp`)
- Kolom `logo` di database diperbarui ke path logo baru

**Hasil Uji**: ⬜ BELUM

---

### TC-EG-06 — Edit Gerai: Inisial Unik Kecuali Milik Sendiri

| Atribut       | Detail                                                            |
| ------------- | ----------------------------------------------------------------- |
| **ID**        | TC-EG-06                                                          |
| **Fitur**     | Edit Gerai — Validasi Unique Inisial (Ignore Self)                |
| **Teknik**    | Branch Coverage → `'unique:departments,inisial,'.$department->id` |
| **Path Kode** | `GeraiLoketController.php:99`                                     |
| **Prioritas** | Tinggi                                                            |

**Test Data**:

| Skenario                         | Inisial diisi         | Expected                |
| -------------------------------- | --------------------- | ----------------------- |
| Simpan tanpa ubah inisial        | "DDK" (milik sendiri) | ✅ Sukses (ignore self) |
| Ubah inisial ke milik gerai lain | "DSP" (sudah ada)     | ❌ Error unique         |

**Langkah Uji (per skenario)**:

1. Edit gerai, ubah/pertahankan inisial sesuai skenario
2. Klik "Simpan"

**Expected Result (skenario 1)**: Form berhasil disimpan meski inisial sama (Rule `unique` mengabaikan ID sendiri)  
**Expected Result (skenario 2)**: Error validasi inisial sudah dipakai gerai lain

**Hasil Uji**: ⬜ BELUM

---

## Area 6 — Hapus Gerai

> **Kode yang diuji**: `GeraiLoketController@destroyDepartment()` L142–164

### TC-DG-01 — Hapus Gerai: Berhasil

| Atribut       | Detail                             |
| ------------- | ---------------------------------- |
| **ID**        | TC-DG-01                           |
| **Fitur**     | Hapus Gerai — Happy Path           |
| **Teknik**    | Statement Coverage                 |
| **Path Kode** | `GeraiLoketController.php:142–164` |
| **Prioritas** | Tinggi                             |

**Langkah Uji**:

1. Klik **"Hapus"** pada baris gerai di tabel
2. Dialog konfirmasi muncul → klik OK
3. Amati tabel dan card Total Gerai

**Expected Result**:

- Gerai tidak lagi muncul di tabel
- Flash success: `"Gerai {name} berhasil dihapus."`
- Card Total Gerai berkurang 1
- Audit log event `department_deleted` tercatat

**Verification Query**:

```sql
SELECT * FROM departments WHERE name = '{nama_gerai_yang_dihapus}';
-- Harusnya: 0 baris
```

**Hasil Uji**: ⬜ BELUM

---

### TC-DG-02 — Hapus Gerai: Dialog Konfirmasi Muncul

| Atribut       | Detail                                                |
| ------------- | ----------------------------------------------------- |
| **ID**        | TC-DG-02                                              |
| **Fitur**     | Hapus Gerai — Konfirmasi Browser                      |
| **Teknik**    | Statement Coverage → `onsubmit="return confirm(...)"` |
| **Path Kode** | `table.blade.php:37` + `cards.blade.php:25`           |
| **Prioritas** | Tinggi                                                |

**Langkah Uji**:

1. Klik "Hapus" pada gerai manapun
2. Amati apakah dialog konfirmasi muncul

**Expected Result**: Browser native `confirm()` dialog muncul dengan pesan: _"Apakah Anda yakin ingin menghapus Gerai ini? Semua loket dan layanan terkait akan ikut terhapus."_

**Hasil Uji**: ⬜ BELUM

---

### TC-DG-03 — Hapus Gerai: Dibatalkan → Data Tidak Terhapus

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-DG-03                                                        |
| **Fitur**     | Hapus Gerai — Batal via Konfirmasi                              |
| **Teknik**    | Branch Coverage → `confirm()` = FALSE                           |
| **Path Kode** | `table.blade.php:37` — `onsubmit="return confirm(...)"` = false |
| **Prioritas** | Sedang                                                          |

**Langkah Uji**:

1. Klik "Hapus" pada gerai manapun
2. Pada dialog konfirmasi, klik **"Cancel"** (bukan OK)
3. Amati tabel

**Expected Result**:

- Form hapus **tidak disubmit** (karena `confirm()` mengembalikan `false`)
- Gerai tetap ada di tabel
- Tidak ada request ke server

**Hasil Uji**: ⬜ BELUM

---

### TC-DG-04 — Hapus Gerai: Logo File Ikut Dihapus dari Storage

| Atribut       | Detail                                                    |
| ------------- | --------------------------------------------------------- |
| **ID**        | TC-DG-04                                                  |
| **Fitur**     | Hapus Gerai — Cleanup File Logo                           |
| **Teknik**    | Branch Coverage → `if ($department->logo)` sebelum delete |
| **Path Kode** | `GeraiLoketController.php:149–151`                        |
| **Prioritas** | Sedang                                                    |

**Precondition**: Ada gerai dengan logo yang tersimpan di storage

**Langkah Uji**:

1. Catat nama file logo dari database
2. Hapus gerai tersebut
3. Periksa apakah file logo masih ada di `storage/app/public/logos/`

**Expected Result**:

- File logo **terhapus** dari storage (tidak ada file orphan)
- Database record gerai juga terhapus

**Hasil Uji**: ⬜ BELUM

---

### TC-DG-05 — Hapus Gerai: Tanpa Logo → Tidak Error

| Atribut       | Detail                                                      |
| ------------- | ----------------------------------------------------------- |
| **ID**        | TC-DG-05                                                    |
| **Fitur**     | Hapus Gerai — Branch Logo NULL                              |
| **Teknik**    | Branch Coverage → `if ($department->logo)` = FALSE          |
| **Path Kode** | `GeraiLoketController.php:149` — kondisi FALSE, skip delete |
| **Prioritas** | Sedang                                                      |

**Precondition**: Ada gerai dengan `logo = NULL`

**Langkah Uji**:

1. Hapus gerai tanpa logo
2. Konfirmasi dialog

**Expected Result**: Gerai berhasil dihapus tanpa error (tidak ada percobaan delete file yang NULL)

**Hasil Uji**: ⬜ BELUM

---

### TC-DG-06 — Hapus Gerai: Audit Log Dicatat Sebelum Hapus

| Atribut       | Detail                                                             |
| ------------- | ------------------------------------------------------------------ |
| **ID**        | TC-DG-06                                                           |
| **Fitur**     | Hapus Gerai — Audit Trail                                          |
| **Teknik**    | Statement Coverage → `AuditLogger::log('department_deleted', ...)` |
| **Path Kode** | `GeraiLoketController.php:153–158`                                 |
| **Prioritas** | Sedang                                                             |

**Langkah Uji**:

1. Hapus satu gerai
2. Buka log aktivitas di sistem

**Expected Result**: Log audit mencatat event `department_deleted` dengan snapshot data gerai sebelum dihapus, meski record di tabel `departments` sudah tidak ada

**Hasil Uji**: ⬜ BELUM

---

## Area 7 — Modal: Penutupan & Reset State

> **Kode yang diuji**: JS `closeGeraiModal()` + interaksi backdrop

### TC-MO-01 — Modal Tutup: Tombol X (Close)

| Atribut       | Detail                                              |
| ------------- | --------------------------------------------------- |
| **ID**        | TC-MO-01                                            |
| **Fitur**     | Modal — Tutup via Tombol X                          |
| **Teknik**    | Statement Coverage → `closeGeraiModal()`            |
| **Path Kode** | `modal.blade.php:11–15` + `modal.blade.php:144–147` |
| **Prioritas** | Tinggi                                              |

**Langkah Uji**:

1. Buka modal (Tambah atau Edit)
2. Isi beberapa field
3. Klik tombol **"×"** (close) di sudut kanan atas

**Expected Result**:

- Modal tersembunyi (class `hidden` ditambahkan ke `#gerai-modal`)
- `clearLogoSelection()` dipanggil (preview logo di-reset)

**Hasil Uji**: ⬜ BELUM

---

### TC-MO-02 — Modal Tutup: Klik Backdrop/Overlay

| Atribut       | Detail                                                           |
| ------------- | ---------------------------------------------------------------- |
| **ID**        | TC-MO-02                                                         |
| **Fitur**     | Modal — Tutup via Klik Backdrop                                  |
| **Teknik**    | Statement Coverage → `onclick="closeGeraiModal()"` pada backdrop |
| **Path Kode** | `modal.blade.php:5`                                              |
| **Prioritas** | Sedang                                                           |

**Langkah Uji**:

1. Buka modal
2. Klik area gelap di luar konten modal (backdrop/overlay)

**Expected Result**: Modal tertutup (sama seperti klik tombol ×)

**Hasil Uji**: ⬜ BELUM

---

### TC-MO-03 — Modal Tutup: Tombol "Batal"

| Atribut       | Detail                                             |
| ------------- | -------------------------------------------------- |
| **ID**        | TC-MO-03                                           |
| **Fitur**     | Modal — Tutup via Tombol Batal                     |
| **Teknik**    | Statement Coverage → `onclick="closeGeraiModal()"` |
| **Path Kode** | `modal.blade.php:55`                               |
| **Prioritas** | Sedang                                             |

**Langkah Uji**:

1. Buka modal
2. Klik tombol **"Batal"** di footer modal

**Expected Result**: Modal tertutup tanpa menyimpan data apapun

**Hasil Uji**: ⬜ BELUM

---

## Area 8 — Keamanan & Otorisasi

### TC-SEC-01 — Akses Halaman: Hanya Super Admin

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-SEC-01                                                    |
| **Fitur**     | Otorisasi — Gate Policy                                      |
| **Teknik**    | Branch Coverage → `$this->authorize('viewAny', User::class)` |
| **Path Kode** | `GeraiLoketController.php:23` + `UserPolicy.php:26–29`       |
| **Prioritas** | Kritis                                                       |

**Langkah Uji**:

1. Login sebagai role selain `super_admin` (mis. `admin_fo`, `admin_gerai`, `pengunjung`)
2. Akses langsung URL `/konfigurasi-gerai-loket`

**Expected Result**: Redirect ke halaman 403 Forbidden atau dashboard, bukan halaman konfigurasi gerai

**Hasil Uji**: ⬜ BELUM

---

### TC-SEC-02 — Akses API Store: Non-Super Admin Ditolak

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-SEC-02                                                    |
| **Fitur**     | Otorisasi — POST storeDepartment                             |
| **Teknik**    | Branch Coverage → `$this->authorize('viewAny', ...)` = FALSE |
| **Path Kode** | `GeraiLoketController.php:57`                                |
| **Prioritas** | Kritis                                                       |

**Langkah Uji**:

1. Login sebagai `admin_gerai` atau `admin_fo`
2. Kirim POST request ke `/konfigurasi-gerai-loket/departments` via Postman dengan data valid

**Expected Result**: HTTP 403 Forbidden — data gerai tidak tersimpan

**Hasil Uji**: ⬜ BELUM

---

### TC-SEC-03 — Akses API Update: Non-Super Admin Ditolak

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-SEC-03                                                    |
| **Fitur**     | Otorisasi — PUT updateDepartment                             |
| **Teknik**    | Branch Coverage → `$this->authorize('viewAny', ...)` = FALSE |
| **Path Kode** | `GeraiLoketController.php:95`                                |
| **Prioritas** | Kritis                                                       |

**Langkah Uji**:

1. Login sebagai non-super_admin
2. Kirim PUT request ke `/konfigurasi-gerai-loket/departments/{id}` via Postman

**Expected Result**: HTTP 403 Forbidden

**Hasil Uji**: ⬜ BELUM

---

### TC-SEC-04 — Akses API Destroy: Non-Super Admin Ditolak

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-SEC-04                                                    |
| **Fitur**     | Otorisasi — DELETE destroyDepartment                         |
| **Teknik**    | Branch Coverage → `$this->authorize('viewAny', ...)` = FALSE |
| **Path Kode** | `GeraiLoketController.php:144`                               |
| **Prioritas** | Kritis                                                       |

**Langkah Uji**:

1. Login sebagai non-super_admin
2. Kirim DELETE request ke `/konfigurasi-gerai-loket/departments/{id}` via Postman

**Expected Result**: HTTP 403 Forbidden — data tidak terhapus

**Hasil Uji**: ⬜ BELUM

---

## Ringkasan Coverage

| Area                  | Jumlah TC | Branch Covered                                                                | Priority Kritis                                  |
| --------------------- | --------- | ----------------------------------------------------------------------------- | ------------------------------------------------ |
| Statistik Cards       | 5         | `count()`, `where('role', AdminGerai)`, hitungan after CRUD                   | TC-M-01, TC-M-04                                 |
| Tabel Gerai (Desktop) | 6         | `@forelse/@empty`, `@if(logo)`, `?? '-'`, `latest()` ordering                 | TC-T-01, TC-T-05                                 |
| Cards Gerai (Mobile)  | 3         | `md:hidden`, `@if(logo)`, `@if(description)`                                  | TC-C-01                                          |
| Modal Tambah          | 12        | `hasFile('logo')`, validasi required/unique/max, konversi WebP, preview JS    | TC-AG-02, TC-AG-04, TC-AG-06, TC-AG-08, TC-AG-11 |
| Modal Edit            | 6         | `openEditGeraiModal()` populate, logo existing, unique ignore self, logo swap | TC-EG-01, TC-EG-04, TC-EG-06                     |
| Hapus Gerai           | 6         | `confirm()`, file logo cleanup, audit before delete                           | TC-DG-01, TC-DG-02                               |
| Penutupan Modal       | 3         | Backdrop click, tombol ×, tombol Batal                                        | TC-MO-01                                         |
| Keamanan              | 4         | `UserPolicy@viewAny` semua HTTP method                                        | TC-SEC-01, TC-SEC-02, TC-SEC-03, TC-SEC-04       |
| **TOTAL**             | **45**    |                                                                               |                                                  |

---

## Template Laporan Hasil Uji

Gunakan tabel berikut saat pelaksanaan pengujian:

| ID TC     | Nama Fitur                                 | Status   | Tanggal Uji | Penguji | Keterangan |
| --------- | ------------------------------------------ | -------- | ----------- | ------- | ---------- |
| TC-M-01   | Total Gerai Instansi Valid                 | ⬜ BELUM | —           | —       | —          |
| TC-M-02   | Angka Bertambah Setelah Tambah             | ⬜ BELUM | —           | —       | —          |
| TC-M-03   | Angka Berkurang Setelah Hapus              | ⬜ BELUM | —           | —       | —          |
| TC-M-04   | Total Petugas Valid (admin_gerai)          | ⬜ BELUM | —           | —       | —          |
| TC-M-05   | Role Lain Tidak Dihitung Petugas           | ⬜ BELUM | —           | —       | —          |
| TC-T-01   | Kolom Nama & Kode Prefix Tampil            | ⬜ BELUM | —           | —       | —          |
| TC-T-02   | Logo Ada → Tampilkan Gambar                | ⬜ BELUM | —           | —       | —          |
| TC-T-03   | Logo Tidak Ada → Placeholder Inisial       | ⬜ BELUM | —           | —       | —          |
| TC-T-04   | Deskripsi Kosong → Dash                    | ⬜ BELUM | —           | —       | —          |
| TC-T-05   | Empty State Saat Tidak Ada Gerai           | ⬜ BELUM | —           | —       | —          |
| TC-T-06   | Data Diurutkan Terbaru                     | ⬜ BELUM | —           | —       | —          |
| TC-C-01   | Cards Tampil di Mobile                     | ⬜ BELUM | —           | —       | —          |
| TC-C-02   | Logo & Inisial pada Mobile                 | ⬜ BELUM | —           | —       | —          |
| TC-C-03   | Deskripsi Conditional Mobile               | ⬜ BELUM | —           | —       | —          |
| TC-AG-01  | Modal Tambah: Form Kosong                  | ⬜ BELUM | —           | —       | —          |
| TC-AG-02  | Tambah Gerai Berhasil Lengkap              | ⬜ BELUM | —           | —       | —          |
| TC-AG-03  | Tambah Gerai Tanpa Logo & Deskripsi        | ⬜ BELUM | —           | —       | —          |
| TC-AG-04  | Validasi Nama Wajib Diisi                  | ⬜ BELUM | —           | —       | —          |
| TC-AG-05  | Validasi Kode Prefix Wajib Diisi           | ⬜ BELUM | —           | —       | —          |
| TC-AG-06  | Validasi Kode Prefix Unik                  | ⬜ BELUM | —           | —       | —          |
| TC-AG-07  | Validasi Kode Prefix Maks 6 Karakter       | ⬜ BELUM | —           | —       | —          |
| TC-AG-08  | Validasi Logo Format & Ukuran              | ⬜ BELUM | —           | —       | —          |
| TC-AG-09  | Logo Dikonversi ke WebP                    | ⬜ BELUM | —           | —       | —          |
| TC-AG-10  | Preview Logo Tampil Saat Dipilih           | ⬜ BELUM | —           | —       | —          |
| TC-AG-11  | Alert Jika Ukuran Logo > 2MB               | ⬜ BELUM | —           | —       | —          |
| TC-AG-12  | Hapus Pilihan Logo                         | ⬜ BELUM | —           | —       | —          |
| TC-EG-01  | Modal Edit: Data Ter-populate              | ⬜ BELUM | —           | —       | —          |
| TC-EG-02  | Modal Edit: Preview Logo Existing          | ⬜ BELUM | —           | —       | —          |
| TC-EG-03  | Modal Edit: Tanpa Logo Preview Tersembunyi | ⬜ BELUM | —           | —       | —          |
| TC-EG-04  | Edit Gerai Berhasil                        | ⬜ BELUM | —           | —       | —          |
| TC-EG-05  | Logo Lama Dihapus Saat Upload Baru         | ⬜ BELUM | —           | —       | —          |
| TC-EG-06  | Validasi Inisial Unique Ignore Self        | ⬜ BELUM | —           | —       | —          |
| TC-DG-01  | Hapus Gerai Berhasil                       | ⬜ BELUM | —           | —       | —          |
| TC-DG-02  | Dialog Konfirmasi Hapus Muncul             | ⬜ BELUM | —           | —       | —          |
| TC-DG-03  | Batal Hapus → Data Tetap Ada               | ⬜ BELUM | —           | —       | —          |
| TC-DG-04  | Logo File Ikut Terhapus                    | ⬜ BELUM | —           | —       | —          |
| TC-DG-05  | Hapus Gerai Tanpa Logo Tidak Error         | ⬜ BELUM | —           | —       | —          |
| TC-DG-06  | Audit Log Hapus Tercatat                   | ⬜ BELUM | —           | —       | —          |
| TC-MO-01  | Modal Tutup via Tombol ×                   | ⬜ BELUM | —           | —       | —          |
| TC-MO-02  | Modal Tutup via Backdrop                   | ⬜ BELUM | —           | —       | —          |
| TC-MO-03  | Modal Tutup via Tombol Batal               | ⬜ BELUM | —           | —       | —          |
| TC-SEC-01 | Akses Halaman Hanya Super Admin            | ⬜ BELUM | —           | —       | —          |
| TC-SEC-02 | POST storeDepartment: Non-SA Ditolak       | ⬜ BELUM | —           | —       | —          |
| TC-SEC-03 | PUT updateDepartment: Non-SA Ditolak       | ⬜ BELUM | —           | —       | —          |
| TC-SEC-04 | DELETE destroyDepartment: Non-SA Ditolak   | ⬜ BELUM | —           | —       | —          |

> **Status**: ✅ PASS · ❌ FAIL · ⏭️ SKIP · ⬜ BELUM

---

## Catatan Bug Potensial (Ditemukan saat Analisis White-Box)

> [!WARNING]
> Temuan berikut ditemukan saat analisis kode dan **perlu diverifikasi saat pengujian berlangsung**.

| #   | Lokasi               | Deskripsi                                                                                                                           | Baris |
| --- | -------------------- | ----------------------------------------------------------------------------------------------------------------------------------- | ----- |
| 1   | `table.blade.php:22` | `{{$dept->logo}}` tercetak di dalam div placeholder (branch logo = NULL), sehingga nilai `NULL` atau kosong bisa muncul di tampilan | L22   |

---

_Dokumen ini dibuat berdasarkan analisis kode sumber langsung (white-box). Setiap test case mencantumkan referensi file dan nomor baris kode yang diuji._
