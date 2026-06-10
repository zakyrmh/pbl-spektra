# Test Case — Halaman Pengaturan Sistem

**Metode**: White-Box Testing  
**Halaman Uji**: `/pengaturan-sistem` (super_admin/settings.blade.php)  
**Versi Aplikasi**: MPP Kota Sawahlunto  
**Tanggal Dibuat**: 2026-06-09  
**Dibuat oleh**: QA Engineer

---

> [!IMPORTANT]
> **Metode White-Box Testing** menguji logika internal kode, bukan hanya antarmuka. Setiap test case memetakan ke **path kode spesifik** di controller, model, atau view yang dieksekusi. Teknik yang digunakan: _Statement Coverage_, _Branch Coverage_, dan _Path Coverage_.

---

## Peta Komponen yang Diuji

| Komponen                    | File                    | Baris Kunci |
| --------------------------- | ----------------------- | ----------- |
| Controller `index()`        | `SettingController.php` | L20–31      |
| Controller `update()`       | `SettingController.php` | L36–84      |
| Model `Setting::getVal()`   | `Setting.php`           | L44–51      |
| Model `Setting::setVal()`   | `Setting.php`           | L56–71      |
| Model `Setting::CREATED_AT` | `Setting.php`           | L16         |
| View form identitas         | `settings.blade.php`    | L58–95      |
| View form marquee           | `settings.blade.php`    | L97–127     |
| View form WebSocket         | `settings.blade.php`    | L129–178    |
| View alert success          | `settings.blade.php`    | L20–35      |
| View alert errors           | `settings.blade.php`    | L37–51      |
| Route GET `/pengaturan-sistem` | `web.php`            | L139        |
| Route PUT `/pengaturan-sistem` | `web.php`            | L140        |

---

## Area 1 — Keamanan & Otorisasi

> **Kode yang diuji**: `SettingController@index` L23–25 dan `SettingController@update` L39–41

### TC-SEC-01 — Akses Halaman: Hanya Super Admin

| Atribut       | Detail                                                            |
| ------------- | ----------------------------------------------------------------- |
| **ID**        | TC-SEC-01                                                         |
| **Fitur**     | Otorisasi — GET index()                                           |
| **Teknik**    | Branch Coverage → `Auth::user()->role !== UserRole::SuperAdmin`   |
| **Path Kode** | `SettingController.php:23–25`                                     |
| **Prioritas** | Kritis                                                            |

**Precondition**: Ada akun dengan role selain `super_admin` (mis. `admin_fo`, `admin_gerai`, `pengunjung`)

**Langkah Uji**:

1. Login sebagai role selain `super_admin` (mis. `admin_fo`)
2. Akses langsung URL `/pengaturan-sistem`
3. Amati respons sistem

**Expected Result**:

- HTTP 403 Forbidden dengan pesan `"Anda tidak memiliki hak akses untuk halaman ini."`
- **Tidak** menampilkan halaman pengaturan
- Halaman abort 403 ditampilkan

**Catatan White-Box**: Guard dilakukan secara manual di controller via `abort(403, ...)`, bukan via Policy/Middleware — `SettingController.php:23–25`.

**Hasil Uji**: ⬜ BELUM

---

### TC-SEC-02 — Akses Halaman: Super Admin Diizinkan

| Atribut       | Detail                                                          |
| ------------- | --------------------------------------------------------------- |
| **ID**        | TC-SEC-02                                                       |
| **Fitur**     | Otorisasi — GET index() — Branch TRUE                           |
| **Teknik**    | Branch Coverage → `Auth::user()->role !== UserRole::SuperAdmin` = FALSE |
| **Path Kode** | `SettingController.php:23` — kondisi FALSE, lanjut ke L28–30   |
| **Prioritas** | Kritis                                                          |

**Precondition**: Ada akun dengan role `super_admin`

**Langkah Uji**:

1. Login sebagai `super_admin`
2. Akses URL `/pengaturan-sistem`
3. Amati halaman yang ditampilkan

**Expected Result**:

- HTTP 200 OK
- Halaman **"Pengaturan Sistem"** tampil dengan seluruh section form
- Judul halaman browser: `"Pengaturan Sistem — MPP Kota Sawahlunto"`
- Semua field form ter-populate dengan data dari database (`$settings`)

**Hasil Uji**: ⬜ BELUM

---

### TC-SEC-03 — Akses Tanpa Login: Redirect ke Login

| Atribut       | Detail                                                   |
| ------------- | -------------------------------------------------------- |
| **ID**        | TC-SEC-03                                                |
| **Fitur**     | Otorisasi — Middleware Auth                              |
| **Teknik**    | Statement Coverage → Middleware `auth` pada route group  |
| **Path Kode** | `web.php:139` — route di dalam group `middleware(['auth'])` |
| **Prioritas** | Kritis                                                   |

**Langkah Uji**:

1. Logout dari sistem (atau buka browser mode incognito)
2. Akses langsung URL `/pengaturan-sistem`

**Expected Result**: Redirect ke halaman login — tidak ada akses ke halaman pengaturan tanpa autentikasi

**Hasil Uji**: ⬜ BELUM

---

### TC-SEC-04 — PUT Update: Non-Super Admin Ditolak

| Atribut       | Detail                                                            |
| ------------- | ----------------------------------------------------------------- |
| **ID**        | TC-SEC-04                                                         |
| **Fitur**     | Otorisasi — PUT update()                                          |
| **Teknik**    | Branch Coverage → `Auth::user()->role !== UserRole::SuperAdmin` = TRUE |
| **Path Kode** | `SettingController.php:39–41`                                     |
| **Prioritas** | Kritis                                                            |

**Langkah Uji**:

1. Login sebagai `admin_fo` atau `admin_gerai`
2. Kirim PUT request ke `/pengaturan-sistem` via Postman dengan body data valid:
   ```json
   {
     "app_name": "Hacked",
     "app_logo": "hacked",
     "maintenance_mode": "0",
     "marquee_text": "Hacked",
     "marquee_active": "1",
     "reverb_host": "127.0.0.1",
     "reverb_port": "8080",
     "reverb_scheme": "http",
     "websocket_enabled": "1"
   }
   ```

**Expected Result**:

- HTTP 403 Forbidden
- Data di database **tidak berubah**
- Pesan error: `"Anda tidak memiliki hak akses untuk halaman ini."`

**Hasil Uji**: ⬜ BELUM

---

## Area 2 — Halaman Index: Pengambilan Data Settings

> **Kode yang diuji**: `SettingController@index` L28–30, `Setting::all()->pluck('value', 'key')->all()`

### TC-I-01 — Index: Semua Setting Dimuat ke View

| Atribut       | Detail                                                                               |
| ------------- | ------------------------------------------------------------------------------------ |
| **ID**        | TC-I-01                                                                              |
| **Fitur**     | Halaman Index — Pengambilan Setting                                                   |
| **Teknik**    | Statement Coverage → `Setting::all()->pluck('value', 'key')->all()`                  |
| **Path Kode** | `SettingController.php:28`                                                            |
| **Prioritas** | Tinggi                                                                                |

**Precondition**: Tabel `settings` memiliki key-key: `app_name`, `app_logo`, `maintenance_mode`, `marquee_text`, `marquee_active`, `reverb_host`, `reverb_port`, `reverb_scheme`, `websocket_enabled`

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem` sebagai Super Admin
2. Amati setiap field form di halaman
3. Bandingkan nilai dengan data di database

**Expected Result**:

- Nilai pada field `#app_name` = nilai `value` dari baris `settings` di mana `key = 'app_name'`
- Nilai pada field `#app_logo` = nilai `value` dari baris `settings` di mana `key = 'app_logo'`
- Dropdown `#maintenance_mode` menunjukkan pilihan yang sesuai dengan nilai `maintenance_mode` di database
- `#marquee_text` menampilkan isi teks berjalan dari database
- Dropdown `#marquee_active` menunjukkan pilihan sesuai dengan status aktif/nonaktif
- Field `#reverb_host`, `#reverb_port` terisi sesuai database
- Dropdown `#reverb_scheme` dan `#websocket_enabled` menunjukkan pilihan yang sesuai

**Verification Query**:

```sql
SELECT key, value FROM settings ORDER BY key;
```

**Hasil Uji**: ⬜ BELUM

---

### TC-I-02 — Index: Nilai Default Saat Key Tidak Ada di Database

| Atribut       | Detail                                                                        |
| ------------- | ----------------------------------------------------------------------------- |
| **ID**        | TC-I-02                                                                       |
| **Fitur**     | Halaman Index — Nilai Fallback Null Coalescing                                 |
| **Teknik**    | Branch Coverage → `$settings['key'] ?? ''` = NULL (key tidak ada)             |
| **Path Kode** | `settings.blade.php:73,80,90,113,122,146,155,162,170` — null coalescing `??` |
| **Prioritas** | Sedang                                                                         |

**Precondition**: Salah satu key setting (mis. `reverb_host`) dihapus dari tabel `settings`

**Langkah Uji**:

1. Hapus satu baris dari tabel `settings`:
    ```sql
    DELETE FROM settings WHERE key = 'reverb_host';
    ```
2. Buka halaman `/pengaturan-sistem`
3. Amati field `#reverb_host`

**Expected Result**:

- Field `#reverb_host` tampil kosong (string `''`), bukan error PHP
- Halaman tidak crash atau menampilkan error `Undefined index`
- Field lain tetap tampil normal

**Catatan White-Box**: Blade menggunakan operator `?? ''` atau `?? '0'` / `?? 'http'` sebagai fallback — tidak melempar exception.

**Hasil Uji**: ⬜ BELUM

---

### TC-I-03 — Index: Nilai `old()` Diprioritaskan Setelah Validasi Gagal

| Atribut       | Detail                                                                     |
| ------------- | -------------------------------------------------------------------------- |
| **ID**        | TC-I-03                                                                    |
| **Fitur**     | Halaman Index — Retensi Input dengan `old()`                                |
| **Teknik**    | Path Coverage → `old('app_name', $settings['app_name'] ?? '')` saat error  |
| **Path Kode** | `settings.blade.php:73` — helper `old()` Laravel                           |
| **Prioritas** | Tinggi                                                                      |

**Langkah Uji**:

1. Buka halaman, isi field `#app_name` dengan nilai baru (mis. `"MPP Baru Sawahlunto"`)
2. Kosongkan field `#reverb_host` (agar validasi gagal)
3. Submit form
4. Amati nilai field `#app_name` setelah halaman kembali dengan error

**Expected Result**:

- Field `#app_name` menampilkan nilai yang baru diketik (`"MPP Baru Sawahlunto"`) — **bukan** nilai lama dari database
- Flash error muncul di atas form
- Pengguna tidak perlu mengetik ulang field yang sudah diisi

**Hasil Uji**: ⬜ BELUM

---

## Area 3 — Form Pengaturan Umum & Identitas

> **Kode yang diuji**: `settings.blade.php` L58–95 (section "Pengaturan Umum & Identitas")

### TC-U-01 — Form Identitas: Field Nama Aplikasi Tampil

| Atribut       | Detail                                   |
| ------------- | ---------------------------------------- |
| **ID**        | TC-U-01                                  |
| **Fitur**     | Form Identitas — Field Nama Aplikasi     |
| **Teknik**    | Statement Coverage                       |
| **Path Kode** | `settings.blade.php:71–76`               |
| **Prioritas** | Tinggi                                   |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. Periksa field `#app_name`

**Expected Result**:

- Label: **"Nama Aplikasi / Instansi"**
- Input `type="text"` dengan atribut `required`
- Value ter-populate dari `$settings['app_name']`
- Hint text: `"Nama resmi yang digunakan pada header dan logo navigasi."`

**Hasil Uji**: ⬜ BELUM

---

### TC-U-02 — Form Identitas: Field Path Logo Tampil

| Atribut       | Detail                                 |
| ------------- | -------------------------------------- |
| **ID**        | TC-U-02                                |
| **Fitur**     | Form Identitas — Field Path Logo       |
| **Teknik**    | Statement Coverage                     |
| **Path Kode** | `settings.blade.php:78–83`             |
| **Prioritas** | Tinggi                                 |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. Periksa field `#app_logo`

**Expected Result**:

- Label: **"Path Logo Aplikasi"**
- Input `type="text"` dengan `required` dan styling monospace (`font-mono`)
- Value ter-populate dari `$settings['app_logo']`
- Hint text: `"Lokasi direktori aset berkas gambar logo aplikasi."`

**Hasil Uji**: ⬜ BELUM

---

### TC-U-03 — Form Identitas: Dropdown Mode Pemeliharaan — Nilai Aktif (0) Terpilih

| Atribut       | Detail                                                                             |
| ------------- | ---------------------------------------------------------------------------------- |
| **ID**        | TC-U-03                                                                            |
| **Fitur**     | Form Identitas — Dropdown Status Operasional (maintenance_mode = 0)                |
| **Teknik**    | Branch Coverage → kondisi ternary `=== '0' ? 'selected' : ''` = TRUE              |
| **Path Kode** | `settings.blade.php:90` — `option value="0"` dengan kondisi selected               |
| **Prioritas** | Tinggi                                                                              |

**Precondition**: `settings` tabel memiliki `key = 'maintenance_mode'` dengan `value = '0'`

**Langkah Uji**:

1. Set `maintenance_mode = '0'` di database
2. Buka halaman
3. Periksa dropdown `#maintenance_mode`

**Expected Result**:

- Opsi **"Aktif (Sistem Berjalan Normal)"** terpilih (selected)
- Opsi "Mode Pemeliharaan" tidak terpilih
- Hint text: `"Jika diaktifkan, warga tidak dapat melakukan booking baru di portal publik."`

**Verification Query**:

```sql
SELECT value FROM settings WHERE key = 'maintenance_mode';
-- Harusnya mengembalikan '0'
```

**Hasil Uji**: ⬜ BELUM

---

### TC-U-04 — Form Identitas: Dropdown Mode Pemeliharaan — Nilai Maintenance (1) Terpilih

| Atribut       | Detail                                                                              |
| ------------- | ----------------------------------------------------------------------------------- |
| **ID**        | TC-U-04                                                                             |
| **Fitur**     | Form Identitas — Dropdown Status Operasional (maintenance_mode = 1)                 |
| **Teknik**    | Branch Coverage → kondisi ternary `=== '1' ? 'selected' : ''` = TRUE               |
| **Path Kode** | `settings.blade.php:91` — `option value="1"` dengan kondisi selected                |
| **Prioritas** | Sedang                                                                               |

**Precondition**: `settings` tabel memiliki `key = 'maintenance_mode'` dengan `value = '1'`

**Langkah Uji**:

1. Set `maintenance_mode = '1'` di database:
    ```sql
    UPDATE settings SET value = '1' WHERE key = 'maintenance_mode';
    ```
2. Reload halaman `/pengaturan-sistem`
3. Periksa dropdown `#maintenance_mode`

**Expected Result**:

- Opsi **"Mode Pemeliharaan (Maintenance Mode)"** terpilih
- Opsi "Aktif" tidak terpilih

**Hasil Uji**: ⬜ BELUM

---

## Area 4 — Form Layar Monitor & Marquee Text

> **Kode yang diuji**: `settings.blade.php` L97–127 (section "Layar Monitor & Marquee Text")

### TC-MQ-01 — Form Marquee: Textarea Teks Berjalan Tampil

| Atribut       | Detail                                     |
| ------------- | ------------------------------------------ |
| **ID**        | TC-MQ-01                                   |
| **Fitur**     | Form Marquee — Textarea Teks Berjalan       |
| **Teknik**    | Statement Coverage                         |
| **Path Kode** | `settings.blade.php:110–116`               |
| **Prioritas** | Tinggi                                     |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. Periksa textarea `#marquee_text`

**Expected Result**:

- Label: **"Teks Berjalan Monitor"**
- Textarea dengan `rows="3"`, atribut `required`, dan `placeholder="Contoh: Selamat Datang di Mal Pelayanan Publik..."`
- Isi textarea = nilai `$settings['marquee_text']` dari database
- Hint text: `"Teks pengumuman/imbauan yang akan terus berjalan di bagian bawah monitor."`

**Hasil Uji**: ⬜ BELUM

---

### TC-MQ-02 — Form Marquee: Dropdown Status Marquee — Tampilkan (1) Terpilih

| Atribut       | Detail                                                                         |
| ------------- | ------------------------------------------------------------------------------ |
| **ID**        | TC-MQ-02                                                                       |
| **Fitur**     | Form Marquee — Dropdown Status Running Text (marquee_active = 1)               |
| **Teknik**    | Branch Coverage → kondisi ternary `=== '1' ? 'selected' : ''` = TRUE          |
| **Path Kode** | `settings.blade.php:122` — `option value="1"` selected                         |
| **Prioritas** | Tinggi                                                                          |

**Precondition**: `settings` memiliki `key = 'marquee_active'` dengan `value = '1'`

**Langkah Uji**:

1. Pastikan `marquee_active = '1'` di database
2. Buka halaman, periksa dropdown `#marquee_active`

**Expected Result**: Opsi **"Tampilkan Teks Berjalan"** terpilih

**Hasil Uji**: ⬜ BELUM

---

### TC-MQ-03 — Form Marquee: Dropdown Status Marquee — Sembunyikan (0) Terpilih

| Atribut       | Detail                                                                        |
| ------------- | ----------------------------------------------------------------------------- |
| **ID**        | TC-MQ-03                                                                      |
| **Fitur**     | Form Marquee — Dropdown Status Running Text (marquee_active = 0)              |
| **Teknik**    | Branch Coverage → kondisi ternary `=== '0' ? 'selected' : ''` = TRUE         |
| **Path Kode** | `settings.blade.php:123` — `option value="0"` selected                        |
| **Prioritas** | Sedang                                                                         |

**Precondition**: `settings` memiliki `key = 'marquee_active'` dengan `value = '0'`

**Langkah Uji**:

1. Set `marquee_active = '0'` di database:
    ```sql
    UPDATE settings SET value = '0' WHERE key = 'marquee_active';
    ```
2. Reload halaman dan periksa dropdown `#marquee_active`

**Expected Result**: Opsi **"Sembunyikan Teks Berjalan"** terpilih

**Hasil Uji**: ⬜ BELUM

---

## Area 5 — Form WebSocket & Integrasi Real-Time

> **Kode yang diuji**: `settings.blade.php` L129–178 (section "WebSocket & Integrasi Real-Time")

### TC-WS-01 — Form WebSocket: Dropdown websocket_enabled Tampil Benar

| Atribut       | Detail                                                                         |
| ------------- | ------------------------------------------------------------------------------ |
| **ID**        | TC-WS-01                                                                       |
| **Fitur**     | Form WebSocket — Dropdown Aktifkan Layanan WebSocket                           |
| **Teknik**    | Branch Coverage → kondisi selected `websocket_enabled` = `'1'` vs `'0'`       |
| **Path Kode** | `settings.blade.php:144–149`                                                   |
| **Prioritas** | Tinggi                                                                          |

**Langkah Uji (per skenario)**:

1. Set `websocket_enabled = '1'` di database → opsi **"Aktifkan Koneksi Real-Time"** harus terpilih
2. Set `websocket_enabled = '0'` di database → opsi **"Nonaktifkan (Gunakan Fallback Polling)"** harus terpilih

**Expected Result per skenario**:

| Nilai DB | Opsi Terpilih                              |
| -------- | ------------------------------------------ |
| `'1'`    | Aktifkan Koneksi Real-Time                 |
| `'0'`    | Nonaktifkan (Gunakan Fallback Polling)     |

**Hint text** yang diverifikasi: `"Bila dinonaktifkan, aplikasi akan menggunakan AJAX polling untuk memperbarui monitor."`

**Hasil Uji**: ⬜ BELUM

---

### TC-WS-02 — Form WebSocket: Field reverb_host Tampil Benar

| Atribut       | Detail                                     |
| ------------- | ------------------------------------------ |
| **ID**        | TC-WS-02                                   |
| **Fitur**     | Form WebSocket — Field WebSocket Host      |
| **Teknik**    | Statement Coverage                         |
| **Path Kode** | `settings.blade.php:153–158`               |
| **Prioritas** | Tinggi                                     |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. Periksa field `#reverb_host`

**Expected Result**:

- Label: **"WebSocket Host"**
- Input `type="text"` dengan `required` dan `font-mono`
- Value ter-populate dari `$settings['reverb_host']`
- Hint text: `"Host server (contoh: 127.0.0.1 atau domain)."`

**Hasil Uji**: ⬜ BELUM

---

### TC-WS-03 — Form WebSocket: Field reverb_port Tampil Benar

| Atribut       | Detail                                                         |
| ------------- | -------------------------------------------------------------- |
| **ID**        | TC-WS-03                                                       |
| **Fitur**     | Form WebSocket — Field WebSocket Port                          |
| **Teknik**    | Statement Coverage → input `type="number"` dengan `min` `max` |
| **Path Kode** | `settings.blade.php:160–165`                                   |
| **Prioritas** | Tinggi                                                          |

**Langkah Uji**:

1. Buka halaman, periksa field `#reverb_port`

**Expected Result**:

- Input `type="number"` dengan atribut `min="1"`, `max="65535"`, `required`
- Value ter-populate dari `$settings['reverb_port']`
- Hint text: `"Port WebSocket (default Reverb: 8080)."`

**Hasil Uji**: ⬜ BELUM

---

### TC-WS-04 — Form WebSocket: Dropdown reverb_scheme — HTTP Terpilih

| Atribut       | Detail                                                                              |
| ------------- | ----------------------------------------------------------------------------------- |
| **ID**        | TC-WS-04                                                                            |
| **Fitur**     | Form WebSocket — Dropdown Scheme (http)                                             |
| **Teknik**    | Branch Coverage → kondisi ternary `=== 'http' ? 'selected' : ''` = TRUE            |
| **Path Kode** | `settings.blade.php:171` — `option value="http"` selected                           |
| **Prioritas** | Sedang                                                                               |

**Precondition**: `settings` memiliki `key = 'reverb_scheme'` dengan `value = 'http'`

**Langkah Uji**:

1. Pastikan `reverb_scheme = 'http'` di database
2. Buka halaman dan periksa dropdown `#reverb_scheme`

**Expected Result**: Opsi **"HTTP (Tidak Terenkripsi)"** terpilih

**Hasil Uji**: ⬜ BELUM

---

### TC-WS-05 — Form WebSocket: Dropdown reverb_scheme — HTTPS Terpilih

| Atribut       | Detail                                                                               |
| ------------- | ------------------------------------------------------------------------------------ |
| **ID**        | TC-WS-05                                                                             |
| **Fitur**     | Form WebSocket — Dropdown Scheme (https)                                             |
| **Teknik**    | Branch Coverage → kondisi ternary `=== 'https' ? 'selected' : ''` = TRUE            |
| **Path Kode** | `settings.blade.php:172` — `option value="https"` selected                           |
| **Prioritas** | Sedang                                                                                |

**Precondition**: `settings` memiliki `key = 'reverb_scheme'` dengan `value = 'https'`

**Langkah Uji**:

1. Set `reverb_scheme = 'https'` di database
2. Reload halaman dan periksa dropdown `#reverb_scheme`

**Expected Result**: Opsi **"HTTPS (SSL Secure)"** terpilih

**Hint text** yang diverifikasi: `"Gunakan HTTPS untuk koneksi SSL yang aman di lingkungan produksi."`

**Hasil Uji**: ⬜ BELUM

---

## Area 6 — Proses Update: Happy Path

> **Kode yang diuji**: `SettingController@update` L36–84 — seluruh path saat validasi berhasil

### TC-UP-01 — Update: Berhasil Menyimpan Semua Field

| Atribut       | Detail                                                               |
| ------------- | -------------------------------------------------------------------- |
| **ID**        | TC-UP-01                                                             |
| **Fitur**     | Update Setting — Happy Path (semua field berubah)                    |
| **Teknik**    | Path Coverage — seluruh branch validasi = valid, `$after` tidak kosong |
| **Path Kode** | `SettingController.php:43–83`                                        |
| **Prioritas** | Tinggi                                                                |

**Data Uji**:

| Field              | Nilai Lama (sebelum)        | Nilai Baru (setelah)               |
| ------------------ | --------------------------- | ---------------------------------- |
| `app_name`         | `"MPP Kota Sawahlunto"`     | `"MPP Kota Sawahlunto — Edisi 2"` |
| `app_logo`         | `"images/logo.png"`         | `"images/logo-baru.png"`           |
| `maintenance_mode` | `"0"`                       | `"1"`                              |
| `marquee_text`     | `"Selamat Datang di MPP..."` | `"Pelayanan Terpadu Sawahlunto"`   |
| `marquee_active`   | `"1"`                       | `"0"`                              |
| `reverb_host`      | `"127.0.0.1"`               | `"192.168.1.10"`                   |
| `reverb_port`      | `"8080"`                    | `"6001"`                           |
| `reverb_scheme`    | `"http"`                    | `"https"`                          |
| `websocket_enabled`| `"1"`                       | `"0"`                              |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. Ubah semua field sesuai kolom "Nilai Baru"
3. Klik **"Simpan Pengaturan"**
4. Amati respons

**Expected Result**:

- Redirect ke `/pengaturan-sistem` (route `admin.settings.index`)
- Flash alert hijau muncul: **"Pembaruan Berhasil"** dengan pesan `"Pengaturan sistem berhasil diperbarui."`
- Seluruh field menampilkan nilai baru
- Database diperbarui: semua baris `settings` menunjukkan nilai terbaru

**Verification Query**:

```sql
SELECT key, value FROM settings
WHERE key IN ('app_name','maintenance_mode','reverb_scheme','websocket_enabled');
-- Harusnya mengembalikan nilai baru
```

**Hasil Uji**: ⬜ BELUM

---

### TC-UP-02 — Update: Hanya Field yang Berubah Diupdate (Optimasi Loop)

| Atribut       | Detail                                                                        |
| ------------- | ----------------------------------------------------------------------------- |
| **ID**        | TC-UP-02                                                                      |
| **Fitur**     | Update Setting — Optimasi: skip field tanpa perubahan                          |
| **Teknik**    | Branch Coverage → `if ($oldVal !== $value)` = FALSE (nilai sama)              |
| **Path Kode** | `SettingController.php:62–67` — kondisi FALSE, `setVal()` tidak dipanggil     |
| **Prioritas** | Sedang                                                                         |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. **Jangan ubah** nilai apapun (biarkan semua field tetap sama)
3. Langsung klik **"Simpan Pengaturan"**
4. Periksa kolom `updated_at` di tabel `settings` sebelum dan sesudah

**Expected Result**:

- Redirect sukses dengan flash success
- Namun kolom `updated_at` di tabel `settings` **tidak berubah** — karena tidak ada field yang dimodifikasi
- Array `$after` kosong → blok `AuditLogger::log()` tidak dieksekusi (L70–80)
- **Tidak ada** record audit log baru yang terbuat

**Catatan White-Box**: Loop `foreach ($validated as $key => $value)` pada L58–68 membandingan `$oldVal !== $value`. Jika sama, blok `setVal()` di-skip sehingga cache tidak direset dan `updated_at` tidak berubah.

**Verification Query**:

```sql
SELECT key, updated_at FROM settings ORDER BY key;
-- Bandingkan timestamp sebelum dan sesudah submit tanpa perubahan
```

**Hasil Uji**: ⬜ BELUM

---

### TC-UP-03 — Update: Hanya Satu Field Diubah

| Atribut       | Detail                                                                          |
| ------------- | ------------------------------------------------------------------------------- |
| **ID**        | TC-UP-03                                                                        |
| **Fitur**     | Update Setting — Partial Update (satu field berubah)                             |
| **Teknik**    | Branch Coverage → `if ($oldVal !== $value)` = TRUE hanya untuk 1 key            |
| **Path Kode** | `SettingController.php:62–67` — loop iterasi 9 field, hanya 1 yang masuk blok  |
| **Prioritas** | Sedang                                                                           |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem`
2. Ubah **hanya** field `#marquee_text` (field lain dibiarkan sama)
3. Klik **"Simpan Pengaturan"**

**Expected Result**:

- Redirect sukses dengan flash success
- Hanya `settings` dengan `key = 'marquee_text'` yang memiliki `updated_at` terbaru
- Setting lain tetap memiliki `updated_at` lama
- Audit log `settings_updated` tercatat dengan `before` dan `after` hanya mengandung key `marquee_text`

**Verification Query**:

```sql
SELECT key, value, updated_at FROM settings
WHERE key = 'marquee_text';
```

**Hasil Uji**: ⬜ BELUM

---

### TC-UP-04 — Update: Audit Log Dicatat Saat Ada Perubahan

| Atribut       | Detail                                                                |
| ------------- | --------------------------------------------------------------------- |
| **ID**        | TC-UP-04                                                              |
| **Fitur**     | Update Setting — Audit Trail                                           |
| **Teknik**    | Branch Coverage → `if (!empty($after))` = TRUE                        |
| **Path Kode** | `SettingController.php:70–80`                                          |
| **Prioritas** | Sedang                                                                 |

**Langkah Uji**:

1. Ubah minimal satu field (mis. `app_name`)
2. Klik **"Simpan Pengaturan"**
3. Periksa tabel `audit_logs` (atau sesuai nama tabel log aplikasi)

**Expected Result**:

- Record audit log baru terbuat dengan:
  - `event = 'settings_updated'`
  - `description = 'Super Admin memperbarui konfigurasi pengaturan sistem.'`
  - `properties->before` → nilai lama field yang berubah
  - `properties->after` → nilai baru field yang berubah
- Hanya field yang berubah yang tercatat dalam `before`/`after`

**Verification Query**:

```sql
SELECT event, description, properties, created_at
FROM audit_logs
WHERE event = 'settings_updated'
ORDER BY created_at DESC LIMIT 1;
```

**Hasil Uji**: ⬜ BELUM

---

### TC-UP-05 — Update: Audit Log Tidak Dicatat Saat Tidak Ada Perubahan

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-UP-05                                                     |
| **Fitur**     | Update Setting — Audit Trail Tidak Dieksekusi                 |
| **Teknik**    | Branch Coverage → `if (!empty($after))` = FALSE               |
| **Path Kode** | `SettingController.php:70` — kondisi FALSE, log tidak dibuat  |
| **Prioritas** | Sedang                                                        |

**Langkah Uji**:

1. Catat `COUNT(*)` dari tabel `audit_logs` di mana `event = 'settings_updated'`
2. Submit form tanpa mengubah nilai apapun
3. Periksa `COUNT(*)` kembali

**Expected Result**: Jumlah record `settings_updated` di `audit_logs` **tidak bertambah**

**Hasil Uji**: ⬜ BELUM

---

### TC-UP-06 — Update: Cache Setting Direset Setelah Update

| Atribut       | Detail                                                                              |
| ------------- | ----------------------------------------------------------------------------------- |
| **ID**        | TC-UP-06                                                                            |
| **Fitur**     | Update Setting — Cache Invalidation                                                  |
| **Teknik**    | Statement Coverage → `Cache::forget("setting.{$key}")`                              |
| **Path Kode** | `Setting.php:68` — dipanggil oleh `Setting::setVal()` dari `SettingController.php:66` |
| **Prioritas** | Tinggi                                                                               |

**Langkah Uji**:

1. Catat nilai `app_name` yang sedang aktif di aplikasi (mis. di header navigasi)
2. Update `app_name` via form menjadi `"MPP Kota Sawahlunto — Update"`
3. Buka halaman lain yang menampilkan `app_name` (mis. halaman `/display` atau header)
4. Amati apakah nama aplikasi sudah diperbarui **tanpa harus menunggu cache expire**

**Expected Result**:

- Nama aplikasi langsung berubah menjadi `"MPP Kota Sawahlunto — Update"` di seluruh halaman
- Cache key `setting.app_name` sudah dihapus via `Cache::forget()`, sehingga nilai terbaru dibaca dari database

**Catatan White-Box**: `Setting::setVal()` selalu memanggil `Cache::forget("setting.{$key}")` setelah `updateOrCreate()`. Tanpa ini, nilai lama akan tetap terbaca dari cache meski database sudah diperbarui.

**Hasil Uji**: ⬜ BELUM

---

## Area 7 — Validasi Backend (Update)

> **Kode yang diuji**: `SettingController@update` L43–53 — aturan validasi setiap field

### TC-V-01 — Validasi: app_name Wajib Diisi

| Atribut       | Detail                                                            |
| ------------- | ----------------------------------------------------------------- |
| **ID**        | TC-V-01                                                           |
| **Fitur**     | Validasi Update — `app_name` required                             |
| **Teknik**    | Branch Coverage → `'app_name' => ['required', ...]` gagal         |
| **Path Kode** | `SettingController.php:44`                                        |
| **Prioritas** | Tinggi                                                            |

**Langkah Uji**:

1. Kosongkan field **"Nama Aplikasi / Instansi"**
2. Isi semua field lain dengan data valid
3. Klik **"Simpan Pengaturan"**

**Expected Result**:

- Form tidak berhasil disimpan
- Error validasi muncul: field `app_name` wajib diisi
- Flash alert merah muncul di atas form dengan daftar error
- Field `#app_name` menampilkan nilai kosong (tidak dikembalikan ke nilai DB karena `old()` = `''`)

**Hasil Uji**: ⬜ BELUM

---

### TC-V-02 — Validasi: app_name Maksimal 255 Karakter

| Atribut       | Detail                                                    |
| ------------- | --------------------------------------------------------- |
| **ID**        | TC-V-02                                                   |
| **Fitur**     | Validasi Update — `app_name` max:255                      |
| **Teknik**    | Branch Coverage → `'max:255'` gagal                       |
| **Path Kode** | `SettingController.php:44`                                |
| **Prioritas** | Sedang                                                    |

**Langkah Uji**:

1. Isi field `#app_name` dengan string 256 karakter (1 karakter melebihi batas)
2. Klik **"Simpan Pengaturan"**

**Expected Result**:

- Error validasi: `app_name` tidak boleh melebihi 255 karakter
- Setting tidak disimpan

**Hasil Uji**: ⬜ BELUM

---

### TC-V-03 — Validasi: maintenance_mode Harus Nilai 0 atau 1

| Atribut       | Detail                                                                      |
| ------------- | --------------------------------------------------------------------------- |
| **ID**        | TC-V-03                                                                     |
| **Fitur**     | Validasi Update — `maintenance_mode` in:0,1                                 |
| **Teknik**    | Branch Coverage → `'in:0,1'` gagal                                          |
| **Path Kode** | `SettingController.php:46`                                                  |
| **Prioritas** | Tinggi                                                                       |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT ke `/pengaturan-sistem` dengan `maintenance_mode=2` (nilai di luar `0,1`)
2. Amati respons server

**Expected Result**:

- HTTP 422 Unprocessable Entity (atau redirect dengan error)
- Error validasi: field `maintenance_mode` harus bernilai `0` atau `1`
- Database tidak diperbarui

**Hasil Uji**: ⬜ BELUM

---

### TC-V-04 — Validasi: marquee_text Wajib Diisi

| Atribut       | Detail                                                        |
| ------------- | ------------------------------------------------------------- |
| **ID**        | TC-V-04                                                       |
| **Fitur**     | Validasi Update — `marquee_text` required                     |
| **Teknik**    | Branch Coverage → `'marquee_text' => ['required', ...]` gagal |
| **Path Kode** | `SettingController.php:47`                                    |
| **Prioritas** | Tinggi                                                         |

**Langkah Uji**:

1. Kosongkan textarea `#marquee_text`
2. Klik **"Simpan Pengaturan"**

**Expected Result**:

- Error validasi muncul: field `marquee_text` wajib diisi
- Flash alert error ditampilkan dengan daftar kesalahan

**Hasil Uji**: ⬜ BELUM

---

### TC-V-05 — Validasi: marquee_text Maksimal 500 Karakter

| Atribut       | Detail                                              |
| ------------- | --------------------------------------------------- |
| **ID**        | TC-V-05                                             |
| **Fitur**     | Validasi Update — `marquee_text` max:500            |
| **Teknik**    | Branch Coverage → `'max:500'` gagal                 |
| **Path Kode** | `SettingController.php:47`                          |
| **Prioritas** | Sedang                                              |

**Langkah Uji**:

1. Isi textarea `#marquee_text` dengan teks 501 karakter
2. Klik **"Simpan Pengaturan"**

**Expected Result**: Error validasi — teks berjalan tidak boleh melebihi 500 karakter

**Hasil Uji**: ⬜ BELUM

---

### TC-V-06 — Validasi: reverb_port Harus Integer

| Atribut       | Detail                                                                     |
| ------------- | -------------------------------------------------------------------------- |
| **ID**        | TC-V-06                                                                    |
| **Fitur**     | Validasi Update — `reverb_port` integer                                    |
| **Teknik**    | Branch Coverage → `'integer'` gagal                                        |
| **Path Kode** | `SettingController.php:50`                                                 |
| **Prioritas** | Tinggi                                                                      |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT dengan `reverb_port="abc"` (string, bukan angka)
2. Amati respons

**Expected Result**: Error validasi — field `reverb_port` harus berupa integer

**Hasil Uji**: ⬜ BELUM

---

### TC-V-07 — Validasi: reverb_port Minimal 1

| Atribut       | Detail                                                       |
| ------------- | ------------------------------------------------------------ |
| **ID**        | TC-V-07                                                      |
| **Fitur**     | Validasi Update — `reverb_port` min:1                        |
| **Teknik**    | Branch Coverage → `'min:1'` gagal                            |
| **Path Kode** | `SettingController.php:50`                                   |
| **Prioritas** | Sedang                                                        |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT dengan `reverb_port=0`
2. Amati respons

**Expected Result**: Error validasi — port harus minimal `1`

**Catatan White-Box**: Input HTML `min="1"` di `settings.blade.php:162` memberikan validasi frontend. Backend tetap memvalidasi di `SettingController.php:50`.

**Hasil Uji**: ⬜ BELUM

---

### TC-V-08 — Validasi: reverb_port Maksimal 65535

| Atribut       | Detail                                                        |
| ------------- | ------------------------------------------------------------- |
| **ID**        | TC-V-08                                                       |
| **Fitur**     | Validasi Update — `reverb_port` max:65535                     |
| **Teknik**    | Branch Coverage → `'max:65535'` gagal                         |
| **Path Kode** | `SettingController.php:50`                                    |
| **Prioritas** | Sedang                                                         |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT dengan `reverb_port=65536` (1 lebih dari batas)
2. Amati respons

**Expected Result**: Error validasi — port tidak boleh melebihi `65535` (batas port TCP/IP)

**Hasil Uji**: ⬜ BELUM

---

### TC-V-09 — Validasi: reverb_scheme Harus http atau https

| Atribut       | Detail                                                            |
| ------------- | ----------------------------------------------------------------- |
| **ID**        | TC-V-09                                                           |
| **Fitur**     | Validasi Update — `reverb_scheme` in:http,https                   |
| **Teknik**    | Branch Coverage → `'in:http,https'` gagal                         |
| **Path Kode** | `SettingController.php:51`                                        |
| **Prioritas** | Tinggi                                                             |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT dengan `reverb_scheme=ftp` (nilai di luar `http,https`)
2. Amati respons

**Expected Result**: Error validasi — scheme hanya boleh `http` atau `https`

**Hasil Uji**: ⬜ BELUM

---

### TC-V-10 — Validasi: websocket_enabled Harus 0 atau 1

| Atribut       | Detail                                                         |
| ------------- | -------------------------------------------------------------- |
| **ID**        | TC-V-10                                                        |
| **Fitur**     | Validasi Update — `websocket_enabled` in:0,1                   |
| **Teknik**    | Branch Coverage → `'in:0,1'` gagal                             |
| **Path Kode** | `SettingController.php:52`                                     |
| **Prioritas** | Tinggi                                                          |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT dengan `websocket_enabled=true` (bukan `0` atau `1`)
2. Amati respons

**Expected Result**: Error validasi — field `websocket_enabled` harus bernilai `0` atau `1`

**Hasil Uji**: ⬜ BELUM

---

### TC-V-11 — Validasi: Semua Field Wajib — Tidak Ada yang Boleh Dikosongkan

| Atribut       | Detail                                                                |
| ------------- | --------------------------------------------------------------------- |
| **ID**        | TC-V-11                                                               |
| **Fitur**     | Validasi Update — Semua Field Required Sekaligus                       |
| **Teknik**    | Path Coverage — semua branch `required` gagal bersamaan               |
| **Path Kode** | `SettingController.php:43–53` — seluruh aturan `required`             |
| **Prioritas** | Sedang                                                                 |

**Langkah Uji**:

1. Manipulasi request via Postman: kirim PUT ke `/pengaturan-sistem` dengan body kosong `{}`
2. Amati respons

**Expected Result**:

- HTTP 422 Unprocessable Entity (atau redirect balik dengan errors)
- Semua 9 field melaporkan error `required` dalam satu respons
- Database **sama sekali tidak tersentuh**

**Hasil Uji**: ⬜ BELUM

---

## Area 8 — Alert & Flash Message di View

> **Kode yang diuji**: `settings.blade.php` L20–51 — dua blok alert Blade

### TC-AL-01 — Alert Success: Tampil Setelah Update Berhasil

| Atribut       | Detail                                                                 |
| ------------- | ---------------------------------------------------------------------- |
| **ID**        | TC-AL-01                                                               |
| **Fitur**     | Alert — Flash Success setelah update                                    |
| **Teknik**    | Branch Coverage → `@if (session('success'))` = TRUE                    |
| **Path Kode** | `settings.blade.php:20–35`                                             |
| **Prioritas** | Tinggi                                                                  |

**Langkah Uji**:

1. Update salah satu pengaturan dengan data valid
2. Amati halaman setelah redirect

**Expected Result**:

- Alert hijau muncul di bagian atas konten
- Header alert: **"Pembaruan Berhasil"**
- Pesan: `"Pengaturan sistem berhasil diperbarui."`
- Ada tombol **×** untuk menutup alert (via `onclick="this.closest('[role=alert]').remove()"`)
- Alert menampilkan animasi `animate-pulse`

**Hasil Uji**: ⬜ BELUM

---

### TC-AL-02 — Alert Success: Tidak Tampil Saat Tidak Ada Flash

| Atribut       | Detail                                                             |
| ------------- | ------------------------------------------------------------------ |
| **ID**        | TC-AL-02                                                           |
| **Fitur**     | Alert — Flash Success tidak ada                                     |
| **Teknik**    | Branch Coverage → `@if (session('success'))` = FALSE               |
| **Path Kode** | `settings.blade.php:20` — kondisi FALSE, blok tidak dirender       |
| **Prioritas** | Rendah                                                              |

**Langkah Uji**:

1. Akses `/pengaturan-sistem` secara langsung (tanpa melalui redirect setelah update)
2. Amati area di atas form

**Expected Result**: Tidak ada alert hijau yang ditampilkan — area bersih tanpa pesan apapun

**Hasil Uji**: ⬜ BELUM

---

### TC-AL-03 — Alert Error: Tampil Saat Validasi Gagal

| Atribut       | Detail                                                              |
| ------------- | ------------------------------------------------------------------- |
| **ID**        | TC-AL-03                                                            |
| **Fitur**     | Alert — Error Validasi                                               |
| **Teknik**    | Branch Coverage → `@if ($errors->any())` = TRUE                     |
| **Path Kode** | `settings.blade.php:37–51`                                          |
| **Prioritas** | Tinggi                                                               |

**Langkah Uji**:

1. Kosongkan field `#app_name`
2. Submit form
3. Amati halaman yang kembali dengan error

**Expected Result**:

- Alert merah muncul di atas form
- Header alert: **"Gagal Memperbarui Pengaturan"**
- Daftar error ditampilkan dalam `<ul>` dengan format `<li>` (via `@foreach ($errors->all() as $err)`)
- Setiap pesan error tampil dalam satu baris `<li>`

**Hasil Uji**: ⬜ BELUM

---

### TC-AL-04 — Alert Error: Tidak Tampil Saat Halaman Baru Dibuka

| Atribut       | Detail                                                        |
| ------------- | ------------------------------------------------------------- |
| **ID**        | TC-AL-04                                                      |
| **Fitur**     | Alert — Error tidak ada saat halaman fresh                     |
| **Teknik**    | Branch Coverage → `@if ($errors->any())` = FALSE              |
| **Path Kode** | `settings.blade.php:37` — kondisi FALSE, blok tidak dirender  |
| **Prioritas** | Rendah                                                         |

**Langkah Uji**:

1. Buka halaman `/pengaturan-sistem` secara langsung (bukan dari redirect error)
2. Amati area di atas form

**Expected Result**: Tidak ada alert merah — form tampil bersih tanpa pesan error

**Hasil Uji**: ⬜ BELUM

---

### TC-AL-05 — Alert Success: Ditutup via Tombol ×

| Atribut       | Detail                                                                         |
| ------------- | ------------------------------------------------------------------------------ |
| **ID**        | TC-AL-05                                                                       |
| **Fitur**     | Alert Success — Mekanisme Tutup                                                 |
| **Teknik**    | Statement Coverage → `onclick="this.closest('[role=alert]').remove()"`          |
| **Path Kode** | `settings.blade.php:29–33`                                                     |
| **Prioritas** | Rendah                                                                          |

**Langkah Uji**:

1. Update pengaturan dengan data valid (alert success muncul)
2. Klik tombol **×** di pojok kanan alert
3. Amati tampilan halaman

**Expected Result**:

- Alert success langsung hilang dari halaman (tanpa reload)
- Elemen `div[role="alert"]` dihapus dari DOM via JavaScript
- Form dan konten lain tetap tidak berubah

**Hasil Uji**: ⬜ BELUM

---

## Area 9 — Model Setting

> **Kode yang diuji**: `Setting.php` — seluruh metode

### TC-MOD-01 — Model::setVal: Upsert — Key Baru → Insert

| Atribut       | Detail                                                                  |
| ------------- | ----------------------------------------------------------------------- |
| **ID**        | TC-MOD-01                                                               |
| **Fitur**     | Model Setting — `setVal()` Insert baru                                  |
| **Teknik**    | Statement Coverage → `updateOrCreate(['key' => $key], [...])` — INSERT  |
| **Path Kode** | `Setting.php:58–65`                                                     |
| **Prioritas** | Sedang                                                                   |

**Precondition**: Tidak ada record dengan key `'app_new_setting'` di tabel `settings`

**Langkah Uji**:

1. Eksekusi via tinker atau unit test:
    ```php
    Setting::setVal('app_new_setting', 'nilai_baru');
    ```
2. Periksa tabel `settings`

**Expected Result**:

- Record baru terbuat di tabel `settings` dengan `key = 'app_new_setting'`, `value = 'nilai_baru'`
- `updated_by` terisi dengan `auth()->id()` saat ini
- Cache key `setting.app_new_setting` di-flush

**Verification Query**:

```sql
SELECT * FROM settings WHERE key = 'app_new_setting';
```

**Hasil Uji**: ⬜ BELUM

---

### TC-MOD-02 — Model::setVal: Upsert — Key Ada → Update

| Atribut       | Detail                                                                  |
| ------------- | ----------------------------------------------------------------------- |
| **ID**        | TC-MOD-02                                                               |
| **Fitur**     | Model Setting — `setVal()` Update existing                               |
| **Teknik**    | Statement Coverage → `updateOrCreate(['key' => $key], [...])` — UPDATE  |
| **Path Kode** | `Setting.php:58–65`                                                     |
| **Prioritas** | Sedang                                                                   |

**Precondition**: Record `app_name` sudah ada di tabel `settings`

**Langkah Uji**:

1. Eksekusi:
    ```php
    Setting::setVal('app_name', 'Nama Baru');
    ```
2. Periksa tabel `settings`

**Expected Result**:

- Record dengan `key = 'app_name'` diupdate (bukan insert duplikat)
- `value = 'Nama Baru'`
- `updated_at` diperbarui ke waktu sekarang
- Tidak ada duplikasi key di tabel

**Hasil Uji**: ⬜ BELUM

---

### TC-MOD-03 — Model::getVal: Cache Hit — Nilai Dibaca dari Cache

| Atribut       | Detail                                                                               |
| ------------- | ------------------------------------------------------------------------------------ |
| **ID**        | TC-MOD-03                                                                            |
| **Fitur**     | Model Setting — `getVal()` Cache Hit                                                  |
| **Teknik**    | Statement Coverage → `Cache::rememberForever()` — cache key sudah ada                |
| **Path Kode** | `Setting.php:46–50`                                                                  |
| **Prioritas** | Rendah                                                                                |

**Langkah Uji**:

1. Panggil `Setting::getVal('app_name')` dua kali berturut-turut
2. Pada panggilan kedua, monitor query database yang dieksekusi (via query log)

**Expected Result**:

- Panggilan pertama: query SQL ke tabel `settings` dieksekusi
- Panggilan kedua: **tidak ada** query SQL (nilai berasal dari cache)
- Kedua panggilan mengembalikan nilai yang sama

**Catatan White-Box**: `Cache::rememberForever("setting.{$key}", ...)` hanya menjalankan closure (query DB) jika cache belum ada. Panggilan kedua langsung mengembalikan nilai dari cache.

**Hasil Uji**: ⬜ BELUM

---

### TC-MOD-04 — Model::getVal: Key Tidak Ada → Kembalikan Default

| Atribut       | Detail                                                                              |
| ------------- | ----------------------------------------------------------------------------------- |
| **ID**        | TC-MOD-04                                                                           |
| **Fitur**     | Model Setting — `getVal()` Key Tidak Ditemukan                                       |
| **Teknik**    | Branch Coverage → `$setting ? $setting->value : $default` — sisi FALSE              |
| **Path Kode** | `Setting.php:49` — `return $setting ? $setting->value : $default`                   |
| **Prioritas** | Sedang                                                                               |

**Langkah Uji**:

1. Panggil dengan key yang tidak ada:
    ```php
    $val = Setting::getVal('non_existing_key', 'default_value');
    ```
2. Amati nilai yang dikembalikan

**Expected Result**:

- Return value = `'default_value'` (parameter `$default`)
- Tidak ada exception atau error

**Hasil Uji**: ⬜ BELUM

---

### TC-MOD-05 — Model::CREATED_AT Null — Tidak Menyimpan created_at

| Atribut       | Detail                                                                          |
| ------------- | ------------------------------------------------------------------------------- |
| **ID**        | TC-MOD-05                                                                       |
| **Fitur**     | Model Setting — Konfigurasi Timestamp                                            |
| **Teknik**    | Statement Coverage → `public const CREATED_AT = null`                           |
| **Path Kode** | `Setting.php:16`                                                                |
| **Prioritas** | Rendah                                                                           |

**Langkah Uji**:

1. Buat record `Setting` baru via `setVal()`
2. Periksa schema tabel `settings` dan nilai `created_at`

**Expected Result**:

- Kolom `created_at` tidak ada di tabel `settings` (atau nullable dan tetap NULL)
- Eloquent tidak mencoba menulis `created_at` saat `INSERT`
- Record berhasil dibuat tanpa error

**Verification Query**:

```sql
DESCRIBE settings;
-- Periksa apakah kolom 'created_at' ada atau tidak
```

**Hasil Uji**: ⬜ BELUM

---

## Ringkasan Coverage

| Area                    | Jumlah TC | Branch Covered                                                                            | Priority Kritis                               |
| ----------------------- | --------- | ----------------------------------------------------------------------------------------- | --------------------------------------------- |
| Keamanan & Otorisasi    | 4         | `role !== SuperAdmin` index & update, middleware auth                                     | TC-SEC-01, TC-SEC-03, TC-SEC-04               |
| Index: Pengambilan Data | 3         | `Setting::all()->pluck()`, `?? ''` fallback, `old()` prioritas                            | TC-I-01, TC-I-03                              |
| Form Identitas          | 4         | Dropdown ternary `maintenance_mode` 0 vs 1                                                | TC-U-01, TC-U-03                              |
| Form Marquee            | 3         | Dropdown ternary `marquee_active` 1 vs 0                                                  | TC-MQ-01, TC-MQ-02                            |
| Form WebSocket          | 5         | Dropdown ternary `websocket_enabled` dan `reverb_scheme` http vs https                    | TC-WS-01, TC-WS-02, TC-WS-03                 |
| Update — Happy Path     | 6         | `$oldVal !== $value` TRUE/FALSE, `!empty($after)` TRUE/FALSE, `Cache::forget()`           | TC-UP-01, TC-UP-04, TC-UP-06                  |
| Validasi Backend        | 11        | `required`, `max:255`, `max:500`, `in:0,1`, `integer`, `min:1`, `max:65535`, `in:http,https` | TC-V-01, TC-V-03, TC-V-06, TC-V-09, TC-V-10 |
| Alert & Flash Message   | 5         | `session('success')` TRUE/FALSE, `$errors->any()` TRUE/FALSE, tombol tutup               | TC-AL-01, TC-AL-03                            |
| Model Setting           | 5         | `updateOrCreate()` INSERT vs UPDATE, `rememberForever()` cache hit, `?: $default`        | TC-MOD-01, TC-MOD-02                          |
| **TOTAL**               | **46**    |                                                                                           |                                               |

---

## Template Laporan Hasil Uji

Gunakan tabel berikut saat pelaksanaan pengujian:

| ID TC      | Nama Fitur                                    | Status   | Tanggal Uji | Penguji | Keterangan |
| ---------- | --------------------------------------------- | -------- | ----------- | ------- | ---------- |
| TC-SEC-01  | Akses Halaman Non-Super Admin → 403            | ⬜ BELUM | —           | —       | —          |
| TC-SEC-02  | Akses Halaman Super Admin → 200                | ⬜ BELUM | —           | —       | —          |
| TC-SEC-03  | Akses Tanpa Login → Redirect Login             | ⬜ BELUM | —           | —       | —          |
| TC-SEC-04  | PUT Update Non-Super Admin → 403               | ⬜ BELUM | —           | —       | —          |
| TC-I-01    | Semua Setting Dimuat ke View                   | ⬜ BELUM | —           | —       | —          |
| TC-I-02    | Nilai Default Saat Key Tidak Ada               | ⬜ BELUM | —           | —       | —          |
| TC-I-03    | Nilai old() Diprioritaskan Setelah Error       | ⬜ BELUM | —           | —       | —          |
| TC-U-01    | Field Nama Aplikasi Tampil Benar               | ⬜ BELUM | —           | —       | —          |
| TC-U-02    | Field Path Logo Tampil Benar                   | ⬜ BELUM | —           | —       | —          |
| TC-U-03    | Dropdown maintenance_mode = 0 Terpilih         | ⬜ BELUM | —           | —       | —          |
| TC-U-04    | Dropdown maintenance_mode = 1 Terpilih         | ⬜ BELUM | —           | —       | —          |
| TC-MQ-01   | Textarea marquee_text Tampil Benar             | ⬜ BELUM | —           | —       | —          |
| TC-MQ-02   | Dropdown marquee_active = 1 Terpilih           | ⬜ BELUM | —           | —       | —          |
| TC-MQ-03   | Dropdown marquee_active = 0 Terpilih           | ⬜ BELUM | —           | —       | —          |
| TC-WS-01   | Dropdown websocket_enabled Tampil Benar        | ⬜ BELUM | —           | —       | —          |
| TC-WS-02   | Field reverb_host Tampil Benar                 | ⬜ BELUM | —           | —       | —          |
| TC-WS-03   | Field reverb_port Tampil Benar                 | ⬜ BELUM | —           | —       | —          |
| TC-WS-04   | Dropdown reverb_scheme = http Terpilih         | ⬜ BELUM | —           | —       | —          |
| TC-WS-05   | Dropdown reverb_scheme = https Terpilih        | ⬜ BELUM | —           | —       | —          |
| TC-UP-01   | Update Berhasil Semua Field                    | ⬜ BELUM | —           | —       | —          |
| TC-UP-02   | Tidak Ada Perubahan → setVal Tidak Dipanggil   | ⬜ BELUM | —           | —       | —          |
| TC-UP-03   | Hanya Satu Field Diubah                        | ⬜ BELUM | —           | —       | —          |
| TC-UP-04   | Audit Log Dicatat Saat Ada Perubahan           | ⬜ BELUM | —           | —       | —          |
| TC-UP-05   | Audit Log Tidak Dicatat Tanpa Perubahan        | ⬜ BELUM | —           | —       | —          |
| TC-UP-06   | Cache Direset Setelah Update                   | ⬜ BELUM | —           | —       | —          |
| TC-V-01    | Validasi app_name Required                     | ⬜ BELUM | —           | —       | —          |
| TC-V-02    | Validasi app_name max:255                      | ⬜ BELUM | —           | —       | —          |
| TC-V-03    | Validasi maintenance_mode in:0,1               | ⬜ BELUM | —           | —       | —          |
| TC-V-04    | Validasi marquee_text Required                 | ⬜ BELUM | —           | —       | —          |
| TC-V-05    | Validasi marquee_text max:500                  | ⬜ BELUM | —           | —       | —          |
| TC-V-06    | Validasi reverb_port Integer                   | ⬜ BELUM | —           | —       | —          |
| TC-V-07    | Validasi reverb_port min:1                     | ⬜ BELUM | —           | —       | —          |
| TC-V-08    | Validasi reverb_port max:65535                 | ⬜ BELUM | —           | —       | —          |
| TC-V-09    | Validasi reverb_scheme in:http,https           | ⬜ BELUM | —           | —       | —          |
| TC-V-10    | Validasi websocket_enabled in:0,1              | ⬜ BELUM | —           | —       | —          |
| TC-V-11    | Validasi Semua Field Kosong Sekaligus          | ⬜ BELUM | —           | —       | —          |
| TC-AL-01   | Alert Success Tampil Setelah Update            | ⬜ BELUM | —           | —       | —          |
| TC-AL-02   | Alert Success Tidak Tampil Fresh Page          | ⬜ BELUM | —           | —       | —          |
| TC-AL-03   | Alert Error Tampil Saat Validasi Gagal         | ⬜ BELUM | —           | —       | —          |
| TC-AL-04   | Alert Error Tidak Tampil Fresh Page            | ⬜ BELUM | —           | —       | —          |
| TC-AL-05   | Alert Success Ditutup via Tombol ×             | ⬜ BELUM | —           | —       | —          |
| TC-MOD-01  | setVal() Insert Key Baru                       | ⬜ BELUM | —           | —       | —          |
| TC-MOD-02  | setVal() Update Key Existing                   | ⬜ BELUM | —           | —       | —          |
| TC-MOD-03  | getVal() Cache Hit Tidak Query DB              | ⬜ BELUM | —           | —       | —          |
| TC-MOD-04  | getVal() Key Tidak Ada → Nilai Default         | ⬜ BELUM | —           | —       | —          |
| TC-MOD-05  | Model CREATED_AT = null → Tidak Simpan Timestamp | ⬜ BELUM | —           | —       | —          |

> **Status**: ✅ PASS · ❌ FAIL · ⏭️ SKIP · ⬜ BELUM

---

_Dokumen ini dibuat berdasarkan analisis kode sumber langsung (white-box). Setiap test case mencantumkan referensi file dan nomor baris kode yang diuji._
