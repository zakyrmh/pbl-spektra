# DESIGN.md — Sistem Manajemen Antrean Digital
## Mal Pelayanan Publik (MPP) Kota Sawahlunto

> **Stack:** Laravel 13 · TailwindCSS 4  
> **Target Pengguna:** Warga berusia 17–50 tahun  
> **Pendekatan:** Civic-Digital — Institusional namun ramah, modern namun dapat dipercaya

---

## 1. Filosofi Desain

Sistem ini melayani masyarakat umum dengan rentang usia dan latar belakang yang luas. Dua nilai utama yang harus dirasakan oleh setiap pengguna:

1. **Kepercayaan** — Tampilan mencerminkan wibawa institusi pemerintah Kota Sawahlunto.
2. **Kemudahan** — Setiap alur (ambil nomor, cek antrean, pantau status) dapat diselesaikan dalam 3 langkah atau kurang.

Secara visual, sistem mengambil referensi dua pendekatan: **ketenangan editorial Cal.com** (kanvas putih, hierarki jelas, ruang napas yang lega) dan **kepercayaan institusional Coinbase** (satu warna aksi utama, tipografi yang terkendali, ritme halaman yang konsisten). Hasilnya adalah identitas **"Civic-Digital"** — bukan desain korporat dingin, bukan pula portal pemerintah usang.

### Prinsip Utama

- **Satu aksi utama per layar.** Setiap halaman memiliki satu `button-primary` yang mendominasi. Pengguna tidak perlu berpikir dua kali.
- **Warna berbicara status.** Hijau = selesai/dipanggil, kuning = menunggu, merah = terlewat/ditutup. Konsisten di seluruh aplikasi.
- **Kepadatan bertahap.** Halaman publik (landing, ambil nomor) lapang seperti Cal.com. Halaman petugas (dashboard, monitor) lebih padat, seperti antarmuka operasional Coinbase.
- **Kontras minimum WCAG AA** pada semua teks dan elemen interaktif.

---

## 2. Sumber Warna — Ekstraksi dari Logo

### Logo MPP Sawahlunto
| Area Logo | Warna Terekstrak | Peran |
|---|---|---|
| Lengkung atas (teal–biru) | `#29ABE2` → `#1A6FAF` | Gradien identitas MPP |
| Lengkung bawah (navy) | `#1A3A8C` | Biru institusional dalam |
| Ikon bangunan (hijau) | `#2E7A3C` | Hijau layanan |
| Gelombang oranye | `#F5A623` | Aksen hangat / peringatan |
| Gelombang hijau-lime | `#72BE44` | Konfirmasi / sukses |
| Teks "Sawahlunto" (putih) | `#FFFFFF` | Teks di atas gelap |

### Lambang Kota Sawahlunto
| Area Lambang | Warna Terekstrak | Peran |
|---|---|---|
| Perisai biru | `#4472C4` | Biru kota (sekunder) |
| Sinar matahari kuning–emas | `#F5C200` | Aksen emas kota |
| Tanah merah | `#B22222` | Semantik bahaya/tutup |
| Dedaunan hijau tua | `#2D6A2D` | Hijau alam |
| Hitam (mahkota, teks) | `#1A1A1A` | Ink / teks utama |

---

## 3. Sistem Warna

### 3.1 Brand & Aksi

| Token | Hex | Keterangan |
|---|---|---|
| `--color-primary` | `#1B4FA8` | Biru institusional utama — semua CTA primer, heading utama. Derivasi dari navy MPP + biru perisai Sawahlunto. |
| `--color-primary-hover` | `#153D88` | State hover/press pada tombol primer. |
| `--color-primary-disabled` | `#9EB4D8` | CTA dalam kondisi disabled. |
| `--color-accent-teal` | `#29ABE2` | Teal cerah dari arc MPP — badge, highlight inline, ikon aktif. Digunakan hemat. |
| `--color-accent-gold` | `#F5C200` | Emas dari lambang kota — elemen dekoratif, nomor antrean VIP/prioritas. Ilustratif, bukan aksi. |

> **Aturan:** `--color-primary` adalah satu-satunya warna aksi. Teal dan emas tidak boleh muncul pada tombol CTA utama.

### 3.2 Permukaan (Surface)

| Token | Hex | Keterangan |
|---|---|---|
| `--color-canvas` | `#FFFFFF` | Lantai halaman default. |
| `--color-surface-soft` | `#F4F6FA` | Band seksi bergantian, latar nav-pill. |
| `--color-surface-card` | `#EFF2F7` | Kartu fitur, kartu antrean. |
| `--color-surface-strong` | `#DDE3EE` | Hairline alternatif, tombol sekunder disabled. |
| `--color-surface-dark` | `#101826` | Footer, hero gelap, kartu tier unggulan. Satu-satunya permukaan gelap di halaman publik. |
| `--color-surface-dark-elevated` | `#1C2A3E` | Kartu tertanam di dalam area gelap. |
| `--color-hairline` | `#D1D9E6` | Border 1px pada permukaan terang. |
| `--color-hairline-soft` | `#E8ECF4` | Pemisah antar seksi yang berbagi kanvas putih. |

### 3.3 Teks

| Token | Hex | Keterangan |
|---|---|---|
| `--color-ink` | `#111827` | Heading dan teks utama. |
| `--color-body` | `#374151` | Teks isi default. |
| `--color-muted` | `#6B7280` | Teks sekunder — sub-judul, label. |
| `--color-muted-soft` | `#9CA3AF` | Teks tersier — caption, fine-print. |
| `--color-on-primary` | `#FFFFFF` | Teks di atas tombol primer. |
| `--color-on-dark` | `#FFFFFF` | Teks di atas permukaan gelap. |
| `--color-on-dark-soft` | `#A8B4C8` | Teks sekunder di permukaan gelap. |

### 3.4 Status Antrean (Semantik)

Status antrean adalah bahasa visual terpenting dalam sistem ini. Konsisten di seluruh monitor, tiket, dan notifikasi.

| Token | Hex | Status | Konteks |
|---|---|---|---|
| `--color-status-waiting` | `#D97706` | Menunggu | Nomor sudah terambil, belum dipanggil. **Amber** — perhatian tanpa kepanikan. |
| `--color-status-called` | `#1B4FA8` | Dipanggil | Sedang dipanggil ke loket. **Biru primer** — tindakan segera. |
| `--color-status-serving` | `#059669` | Dilayani | Sedang dalam proses pelayanan. **Hijau** dari logo MPP. |
| `--color-status-done` | `#6B7280` | Selesai | Proses pelayanan selesai. **Abu muted** — tidak mencolok. |
| `--color-status-skipped` | `#DC2626` | Terlewat | Tidak hadir saat dipanggil. **Merah** dari lambang. |
| `--color-status-closed` | `#374151` | Ditutup | Layanan sudah tutup. **Body dark**. |

> **Aturan:** Warna status **tidak boleh** digunakan sebagai latar belakang tombol aksi. Status adalah informasi, bukan navigasi.

---

## 4. Tipografi

### 4.1 Font Family

| Peran | Font | Fallback | Catatan |
|---|---|---|---|
| Display / Heading | **Plus Jakarta Sans** | `Manrope, system-ui, sans-serif` | Geometris modern, tegak percaya diri, terasa digital namun ramah. Cocok untuk usia 17–50. |
| Body / UI / Tombol | **Geist Sans** | `Inter, system-ui, sans-serif` | Bersih, tinggi keterbacaan di semua ukuran layar. |
| Nomor Antrean / Angka Tabular | **Geist Mono** | `JetBrains Mono, monospace` | Nomor antrean harus monospaced agar tidak bergeser saat berubah. |

> **Mengapa Plus Jakarta Sans?** Font ini diciptakan untuk konteks Asia Tenggara, memiliki karakter huruf yang akrab bagi pengguna berbahasa Indonesia, namun tetap terasa modern dan profesional.

### 4.2 Hirarki Tipografi

| Token | Ukuran | Berat | Line Height | Letter Spacing | Penggunaan |
|---|---|---|---|---|---|
| `text-display-xl` | 56px | 700 | 1.05 | -1.5px | Nomor antrean di monitor utama |
| `text-display-lg` | 44px | 700 | 1.1 | -1px | Heading hero halaman publik |
| `text-display-md` | 36px | 600 | 1.15 | -0.8px | Judul seksi, nama layanan |
| `text-display-sm` | 28px | 600 | 1.2 | -0.5px | Judul kartu layanan, CTA band |
| `text-title-lg` | 22px | 600 | 1.3 | -0.3px | Nama tipe layanan, harga/estimasi |
| `text-title-md` | 18px | 600 | 1.4 | 0 | Judul kartu fitur, label input utama |
| `text-title-sm` | 16px | 600 | 1.4 | 0 | Label list, judul kartu kecil |
| `text-body-md` | 16px | 400 | 1.6 | 0 | Teks isi default |
| `text-body-sm` | 14px | 400 | 1.5 | 0 | Footer, fine-print, label sekunder |
| `text-caption` | 13px | 500 | 1.4 | 0.1px | Badge, caption, timestamp |
| `text-queue-number` | 80px+ | 700 | 1.0 | -3px | **Nomor antrean di layar monitor** — Geist Mono |
| `text-button` | 15px | 600 | 1.0 | 0.1px | Label tombol semua ukuran |
| `text-nav` | 14px | 500 | 1.4 | 0 | Menu navigasi |

### 4.3 Prinsip

- Plus Jakarta Sans **berat 700** untuk semua display. Berat 600 untuk sub-heading. Tidak lebih, tidak kurang.
- Letter-spacing negatif **hanya** pada display. Body dan UI tetap di 0.
- Nomor antrean **selalu** Geist Mono. Angka tidak boleh bergeser saat digit berganti.
- Ukuran teks minimum **16px** untuk body pada halaman publik — mendukung pengguna lansia di rentang usia 40–50.

---

## 5. Tata Letak & Spacing

### 5.1 Sistem Spacing (Base Unit: 4px)

```
xs:  4px   | --spacing-xs
sm:  8px   | --spacing-sm
md:  12px  | --spacing-md
base:16px  | --spacing-base
lg:  24px  | --spacing-lg
xl:  32px  | --spacing-xl
2xl: 48px  | --spacing-2xl
3xl: 64px  | --spacing-3xl
section: 96px | --spacing-section
```

- **Padding seksi:** `96px` vertikal antara band editorial.
- **Padding kartu:** `32px` untuk kartu layanan dan fitur; `24px` untuk kartu antrean dan notifikasi.
- **Gutter grid:** `24px` antar kartu dalam grid 3-kolom; `16px` dalam kolom footer.

### 5.2 Grid & Container

| Konteks | Spesifikasi |
|---|---|
| Lebar konten maksimum | 1280px, centered |
| Grid editorial | 12-kolom |
| Hero split | 6/6 atau 7/5 (judul kiri, visual kanan) |
| Grid kartu layanan | 3-up desktop · 2-up tablet · 1-up mobile |
| Grid monitor antrean | 2-up atau 4-up tergantung jumlah loket aktif |
| Footer | 4-kolom desktop · 2-up tablet · 1-up mobile |

### 5.3 Filosofi Whitespace

Halaman publik mengikuti ritme **lapang**: section padding 96px, kartu tidak berdesakan. Halaman petugas/operator menggunakan **kepadatan terukur** — kartu lebih kecil, grid lebih rapat, namun tetap mengikuti spacing token yang sama.

---

## 6. Elevasi & Kedalaman

| Level | Treatment | Penggunaan |
|---|---|---|
| Flat | Tanpa shadow, tanpa border | Band hero, navigasi atas, area monitor |
| Hairline | `1px solid --color-hairline` | Input, divider tabel, kartu di atas kanvas putih |
| Card soft | `background: --color-surface-card` | Kartu layanan, kartu antrean |
| Card shadow | `0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.07)` | Kartu yang elevated, dropdown, modal |
| Dark inversion | `background: --color-surface-dark` | Footer, hero gelap, kartu tier unggulan |

> Sistem ini **tidak menggunakan** glassmorphism, neumorphism, atau shadow berat. Elevasi bekerja melalui warna permukaan dan satu tier shadow lembut.

---

## 7. Shapes & Border Radius

| Token | Nilai | Penggunaan |
|---|---|---|
| `rounded-xs` | 4px | Tag inline kecil |
| `rounded-sm` | 6px | Badge status dalam tabel |
| `rounded-md` | 8px | Input teks, tombol kecil, dropdown item |
| `rounded-lg` | 12px | Kartu layanan, kartu antrean, kartu fitur |
| `rounded-xl` | 16px | Modal, kartu hero, kartu monitor utama |
| `rounded-2xl` | 24px | Container layar monitor antrean |
| `rounded-pill` | 9999px | Tombol CTA utama, badge status, search bar |
| `rounded-full` | 50% | Avatar petugas, ikon layanan circular |

> **Panduan cepat:** Tombol CTA → `rounded-pill`. Kartu konten → `rounded-lg`. Modal → `rounded-xl`. Avatar → `rounded-full`.

---

## 8. Komponen

### 8.1 Navigasi Atas — `top-nav`

Tinggi **64px**, `background: --color-canvas`, border bawah `--color-hairline`. Layout:
- **Kiri:** Logo MPP Sawahlunto + Logo Kota Sawahlunto (berdampingan, 36px tall)
- **Tengah:** Menu utama — Beranda / Layanan / Antrean Saya / Informasi
- **Kanan:** Tombol "Ambil Nomor" `button-primary` + indikator status loket (buka/tutup)

Pada mode petugas/operator, nav menampilkan nama loket aktif dan tombol "Mode Monitor".

### 8.2 Tombol — Button System

**`button-primary`**
```
background: --color-primary (#1B4FA8)
color: --color-on-primary (#FFFFFF)
font: text-button (15px / 600)
padding: 12px 24px
height: 44px
border-radius: rounded-pill
hover: --color-primary-hover (#153D88)
```

**`button-secondary`**
```
background: --color-canvas
color: --color-ink
border: 1px solid --color-hairline
padding: 12px 24px
height: 44px
border-radius: rounded-pill
```

**`button-ghost`**
```
background: transparent
color: --color-primary
padding: 10px 16px
height: 40px
border-radius: rounded-md
```

**`button-danger`** — khusus aksi destruktif (tutup loket, batalkan nomor)
```
background: --color-status-skipped (#DC2626)
color: #FFFFFF
padding: 12px 24px
height: 44px
border-radius: rounded-pill
```

**`button-icon`** — tombol aksi bulat
```
width/height: 40px
border-radius: rounded-full
background: --color-surface-card
border: 1px solid --color-hairline
```

> **Aturan Aksesibilitas:** Semua tombol memiliki tinggi minimum **44px** sesuai WCAG 2.1 AA untuk target sentuh.

### 8.3 Kartu Layanan — `service-card`

Digunakan pada halaman pemilihan layanan (ambil nomor).

```
background: --color-canvas
border: 1px solid --color-hairline
border-radius: rounded-lg (12px)
padding: 24px
```

Anatomi:
- **Ikon layanan** — 48px, `rounded-full`, latar `--color-surface-soft`, warna ikon `--color-primary`
- **Nama layanan** — `text-title-md` (18px / 600)
- **Estimasi waktu tunggu** — `text-body-sm` + ikon jam, warna `--color-muted`
- **Jumlah antrean saat ini** — Badge `--color-status-waiting` jika ada antrean aktif
- **Tombol "Ambil Nomor"** — `button-primary`, full-width di dalam kartu

**State `service-card--closed`:**
```
opacity: 0.6
border-color: --color-hairline-soft
```
Badge "TUTUP" dengan `background: --color-status-closed`.

### 8.4 Kartu Antrean Pengguna — `queue-ticket-card`

Ditampilkan setelah berhasil mengambil nomor antrean.

```
background: --color-canvas
border: 1px solid --color-hairline
border-radius: rounded-xl (16px)
padding: 32px
box-shadow: 0 4px 16px rgba(27,79,168,0.10)
```

Anatomi:
- **Header:** Logo MPP (kecil) + nama layanan
- **Nomor Antrean** — `text-queue-number` (80px, Geist Mono 700, `--color-primary`)
- **Nama Layanan** — `text-title-md`
- **Status Badge** — pill status sesuai `--color-status-*`
- **Estimasi Waktu** — `text-body-sm`
- **Loket Tujuan** — `text-body-sm`
- **Tombol aksi:** "Refresh Status" (ghost) + "Batalkan" (text-link merah)
- **Footer kartu:** Timestamp ambil nomor, `text-caption`, `--color-muted-soft`

### 8.5 Monitor Antrean — `queue-monitor`

Layar publik yang terpasang di ruang tunggu. Dirancang untuk keterbacaan dari jarak 3–5 meter.

```
background: --color-surface-dark (#101826)
color: --color-on-dark
border-radius: rounded-2xl (24px)
padding: 40px
```

Anatomi:
- **Header monitor:** Logo MPP + "Mal Pelayanan Publik Kota Sawahlunto" — `text-title-lg`, `--color-on-dark-soft`
- **Grid loket:** 2 atau 4 kolom tergantung jumlah loket aktif
- **Kartu loket individual** (`queue-counter-card`):
  ```
  background: --color-surface-dark-elevated (#1C2A3E)
  border-radius: rounded-xl (16px)
  padding: 24px
  ```
  - Nama loket: `text-title-md`, `--color-on-dark-soft`
  - **Nomor dipanggil:** `text-display-xl` (56px, Geist Mono), warna berdasarkan status:
    - Dipanggil: `--color-accent-teal` (#29ABE2) — kontras tinggi di gelap
    - Dilayani: `#72BE44` (hijau lime dari MPP logo)
    - Idle: `--color-on-dark-soft`
  - Animasi: nomor yang baru dipanggil melakukan **pulse** 3x selama 2 detik
- **Ticker bawah:** Informasi jam operasional + pengumuman penting. Auto-scroll horizontal.

### 8.6 Form Input — Input System

**`text-input`**
```
background: --color-canvas
color: --color-ink
font: text-body-md (16px)
border: 1px solid --color-hairline
border-radius: rounded-md (8px)
padding: 12px 16px
height: 48px
```

**`text-input--focus`**
```
border: 2px solid --color-primary
outline: none
box-shadow: 0 0 0 3px rgba(27,79,168,0.12)
```

**`text-input--error`**
```
border: 2px solid --color-status-skipped (#DC2626)
```

**`select-input`** — Sama dengan `text-input`, tambahkan ikon chevron kanan.

**`input-label`** — `text-title-sm` (16px / 600), `--color-ink`, margin bawah 8px.

**`input-helper`** — `text-caption` (13px), `--color-muted`, margin atas 6px.

**`input-error-message`** — `text-caption` (13px), `--color-status-skipped`.

> **Panduan:** Semua input memiliki `<label>` yang terhubung via `for`/`id`. Tidak ada placeholder-only label.

### 8.7 Badge Status — `status-badge`

```
display: inline-flex
align-items: center
gap: 6px
font: text-caption (13px / 500)
padding: 4px 12px
border-radius: rounded-pill
```

| Varian | Background (10% opacity) | Text Color | Dot Color |
|---|---|---|---|
| `badge--waiting` | `rgba(217,119,6,0.12)` | `#92400E` | `#D97706` |
| `badge--called` | `rgba(27,79,168,0.12)` | `#1B4FA8` | `#1B4FA8` |
| `badge--serving` | `rgba(5,150,105,0.12)` | `#065F46` | `#059669` |
| `badge--done` | `rgba(107,114,128,0.10)` | `#374151` | `#6B7280` |
| `badge--skipped` | `rgba(220,38,38,0.12)` | `#991B1B` | `#DC2626` |
| `badge--closed` | `rgba(55,65,81,0.10)` | `#374151` | `#374151` |

Setiap badge memiliki dot indikator (8px, `rounded-full`) di sebelah kiri teks.

### 8.8 Tabel Antrean — `queue-table`

Digunakan pada dashboard petugas dan halaman "Antrean Saya" pengguna.

```
background: --color-canvas
border: 1px solid --color-hairline
border-radius: rounded-lg (12px)
overflow: hidden
```

- **Header tabel:** `background: --color-surface-soft`, `text-title-sm`, `--color-muted`, padding `12px 16px`
- **Row:** padding `14px 16px`, border bawah `--color-hairline-soft`
- **Row highlighted (nomor aktif):** `background: rgba(27,79,168,0.05)`, border kiri `3px solid --color-primary`
- **Kolom nomor:** Geist Mono, `text-title-sm`

### 8.9 Hero Band — Halaman Publik

**`hero-band`** — Halaman utama setelah login/registrasi.
```
background: --color-canvas
padding: 80px 0
```

Layout: 6/6 split:
- **Kiri:** Greeting ("Selamat Pagi, Budi"), sub-heading status antrean aktif, `button-primary` "Ambil Nomor Sekarang"
- **Kanan:** `queue-ticket-card` jika ada nomor aktif, atau ilustrasi langkah-langkah jika tidak ada

**`hero-band-monitor`** — Halaman monitor publik (fullscreen).
```
background: --color-surface-dark
padding: 32px
min-height: 100vh
```

### 8.10 Informasi Estimasi — `wait-info-bar`

Strip informasi di atas halaman pemilihan layanan:
```
background: rgba(27,79,168,0.07)
border: 1px solid rgba(27,79,168,0.15)
border-radius: rounded-md (8px)
padding: 12px 20px
```
Konten: ikon jam + "Estimasi tunggu rata-rata hari ini: **12 menit**" — `text-body-sm`.

### 8.11 Notifikasi / Toast — `toast`

```
background: --color-surface-dark
color: --color-on-dark
border-radius: rounded-lg (12px)
padding: 16px 20px
box-shadow: 0 8px 24px rgba(0,0,0,0.20)
max-width: 360px
```

Varian: `toast--success` (border kiri hijau), `toast--warning` (border kiri amber), `toast--error` (border kiri merah).

### 8.12 Modal — `modal`

```
background: --color-canvas
border-radius: rounded-xl (16px)
padding: 32px
box-shadow: 0 20px 60px rgba(0,0,0,0.15)
max-width: 480px
width: 100%
```

Overlay: `background: rgba(0,0,0,0.50)`.

Anatomi:
- Judul: `text-display-sm` + tombol close `button-icon` pojok kanan atas
- Konten: `text-body-md`
- Footer tombol: row, kanan-rata, gap 12px — sekunder kiri, primer kanan

### 8.13 Navigation Sidebar — Dashboard Petugas

```
background: --color-surface-dark (#101826)
width: 260px
padding: 24px 16px
height: 100vh
position: fixed
```

- **Logo:** berdampingan MPP + Kota Sawahlunto, 32px, di atas
- **Menu item aktif:** `background: rgba(41,171,226,0.15)`, text `--color-accent-teal`, border kiri `3px solid --color-accent-teal`
- **Menu item default:** text `--color-on-dark-soft`, hover `background: rgba(255,255,255,0.06)`
- **Label seksi:** `text-caption`, `--color-on-dark-soft` opacity 50%, huruf kapital semua, spacing `0.1px`

### 8.14 Footer — `footer`

```
background: --color-surface-dark (#101826)
color: --color-on-dark-soft
padding: 64px 0 40px
```

Layout: 4-kolom desktop (MPP, Layanan, Informasi, Kontak).
- Teks copyright: `text-caption`, `--color-muted-soft`
- Kedua logo di footer corner kiri atas
- Tagline Kota Sawahlunto: *"Sawahlunto, Kota Wisata Tambang yang Berbudaya"* — `text-body-sm`, italic

---

## 9. Alur Pengguna & Halaman

### 9.1 Halaman Publik (Pengunjung / Warga)

| Halaman | Path | Tujuan |
|---|---|---|
| Landing / Beranda | `/` | Orientasi, ambil nomor, cek antrean |
| Pilih Layanan | `/layanan` | Grid kartu layanan yang tersedia hari ini |
| Konfirmasi Nomor | `/antrean/konfirmasi` | Tampilkan tiket antrean + QR code |
| Status Antrean | `/antrean/{kode}` | Real-time status nomor pengguna |
| Monitor Publik | `/monitor` | Layar fullscreen untuk ruang tunggu |
| Informasi | `/informasi` | Jam operasional, persyaratan dokumen |

### 9.2 Halaman Petugas / Operator

| Halaman | Path | Tujuan |
|---|---|---|
| Login Petugas | `/petugas/login` | Autentikasi |
| Dashboard Petugas | `/petugas/dashboard` | Panggil nomor, kelola loket |
| Manajemen Loket | `/petugas/loket` | Buka/tutup loket, atur kapasitas |
| Laporan Harian | `/petugas/laporan` | Statistik, rata-rata tunggu, volume |

---

## 10. Palet Warna TailwindCSS 4 — Konfigurasi

Daftarkan custom token di `app.css` (TailwindCSS 4 menggunakan CSS variables native):

```css
/* app.css */
@import "tailwindcss";

@theme {
  /* Brand */
  --color-primary: #1B4FA8;
  --color-primary-hover: #153D88;
  --color-primary-disabled: #9EB4D8;
  --color-accent-teal: #29ABE2;
  --color-accent-gold: #F5C200;

  /* Surface */
  --color-canvas: #FFFFFF;
  --color-surface-soft: #F4F6FA;
  --color-surface-card: #EFF2F7;
  --color-surface-strong: #DDE3EE;
  --color-surface-dark: #101826;
  --color-surface-dark-elevated: #1C2A3E;
  --color-hairline: #D1D9E6;
  --color-hairline-soft: #E8ECF4;

  /* Text */
  --color-ink: #111827;
  --color-body: #374151;
  --color-muted: #6B7280;
  --color-muted-soft: #9CA3AF;
  --color-on-primary: #FFFFFF;
  --color-on-dark: #FFFFFF;
  --color-on-dark-soft: #A8B4C8;

  /* Status Antrean */
  --color-status-waiting: #D97706;
  --color-status-called: #1B4FA8;
  --color-status-serving: #059669;
  --color-status-done: #6B7280;
  --color-status-skipped: #DC2626;
  --color-status-closed: #374151;

  /* Typography */
  --font-display: 'Plus Jakarta Sans', Manrope, system-ui, sans-serif;
  --font-body: 'Geist Sans', Inter, system-ui, sans-serif;
  --font-mono: 'Geist Mono', 'JetBrains Mono', monospace;

  /* Border Radius */
  --radius-xs: 4px;
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-2xl: 24px;
  --radius-pill: 9999px;
  --radius-full: 50%;

  /* Spacing */
  --spacing-section: 96px;
}
```

---

## 11. Aksesibilitas & Inklusivitas

Sistem ini melayani pengguna dari berbagai latar belakang dan kemampuan.

| Standar | Implementasi |
|---|---|
| **WCAG 2.1 AA** | Rasio kontras minimum 4.5:1 pada semua teks. `--color-primary` (#1B4FA8) pada putih: **6.2:1** ✓ |
| **Touch target** | Semua elemen interaktif minimum **44 × 44px** |
| **Font size** | Minimum **16px** pada semua teks yang dibaca (bukan label meta) |
| **Focus visible** | Semua elemen fokusabel memiliki ring `3px solid --color-accent-teal` yang terlihat jelas |
| **Screen reader** | Semua gambar dekoratif `aria-hidden="true"`. Status antrean menggunakan `role="status"` dan `aria-live="polite"` |
| **Warna bukan satu-satunya sinyal** | Status selalu dikomunikasikan dengan ikon + teks + warna (triple redundancy) |
| **Bahasa** | Semua teks dalam Bahasa Indonesia baku yang jelas. Hindari singkatan tanpa penjelasan |

---

## 12. Responsif

### Breakpoint (TailwindCSS 4)

| Nama | Lebar | Perubahan Utama |
|---|---|---|
| `sm` | ≥ 640px | Layout satu kolom; hero stack vertikal |
| `md` | ≥ 768px | Grid 2-kolom; nav pill-group muncul |
| `lg` | ≥ 1024px | Grid 3-kolom; sidebar petugas muncul |
| `xl` | ≥ 1280px | Lebar penuh; max-width 1280px aktif |
| `2xl` | ≥ 1536px | Hanya breathing room lebih lebar |

### Strategi Collapsing

- **Navigasi:** hamburger + drawer full-screen di bawah `md`. Tombol "Ambil Nomor" tetap terlihat.
- **Hero split:** 7/5 kolaps ke single-column di `sm`. Tiket antrean turun ke bawah konten teks.
- **Grid kartu layanan:** 3-up → 2-up → 1-up
- **Monitor antrean:** 4-loket → 2-loket → 1-loket (prioritaskan yang sedang dipanggil)
- **Tabel dashboard:** kolom sekunder tersembunyi di mobile, show/hide via expand row

### Nomor Antrean di Monitor

Ukuran font nomor antrean menyesuaikan viewport:
```
Mobile: 48px
Tablet: 64px  
Desktop: 80px
Large Monitor (≥ 1920px): 96px
```

---

## 13. Animasi & Transisi

Animasi digunakan **hemat dan bertujuan** — bukan dekorasi. Semua animasi menghormati `prefers-reduced-motion`.

| Elemen | Animasi | Durasi | Easing |
|---|---|---|---|
| Nomor antrean baru dipanggil | Scale 1.0 → 1.05 → 1.0 + color flash teal | 600ms | `ease-in-out` |
| Pergantian nomor di monitor | Fade out atas + fade in bawah | 400ms | `ease-out` |
| Tombol hover | Background darken | 150ms | `ease` |
| Modal open | Scale 0.95 + opacity 0 → 1 | 200ms | `ease-out` |
| Toast masuk | Slide in dari kanan | 250ms | `ease-out` |
| Status badge pulse (saat dipanggil) | Opacity 1 → 0.4 → 1 (loop 3x) | 2000ms | `ease-in-out` |
| Card hover (service-card) | `translateY(-2px)` + shadow naik | 200ms | `ease-out` |

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## 14. Ikonografi

Gunakan **Lucide Icons** (kompatibel dengan Laravel + TailwindCSS). Konsisten, line-based, bersih.

| Konteks | Ikon yang Direkomendasikan |
|---|---|
| Antrean / nomor | `ticket`, `hash`, `list-ordered` |
| Status dipanggil | `volume-2`, `megaphone` |
| Status selesai | `check-circle` |
| Status terlewat | `x-circle` |
| Waktu tunggu | `clock` |
| Loket | `briefcase`, `building` |
| Layanan pemerintah | `file-text`, `shield`, `users` |
| Notifikasi | `bell` |
| QR Code | `qr-code` |
| Print tiket | `printer` |

Ukuran standar: **20px** dalam konten, **16px** dalam badge/caption, **24px** dalam navigasi.

---

## 15. Do's dan Don'ts

### ✅ Do

- Gunakan `--color-primary` (#1B4FA8) **hanya** untuk CTA primer dan heading utama.
- Selalu sertakan `status-badge` dengan **ikon + teks + warna** secara bersamaan.
- Nomor antrean **selalu** Geist Mono — jangan pernah gunakan Plus Jakarta Sans untuk angka.
- Akhiri setiap halaman publik dengan `footer` gelap. Transisi terang-ke-gelap adalah ritme editorial sistem.
- Gunakan `rounded-pill` untuk semua tombol CTA dan `rounded-lg` untuk semua kartu konten.
- Pastikan setiap status memiliki teks alternatif selain warna (ikon, label).
- Ukuran font minimum **16px** untuk semua teks yang dibaca pengguna.

### ❌ Don't

- Jangan gunakan `--color-accent-teal` atau `--color-accent-gold` sebagai warna tombol aksi.
- Jangan tempatkan dua `button-primary` bersebelahan — hanya satu CTA utama per layar.
- Jangan gunakan warna status (`--color-status-*`) sebagai latar tombol navigasi.
- Jangan tambahkan permukaan gelap di luar footer dan monitor antrean.
- Jangan gunakan lebih dari dua level heading yang berbeda dalam satu kartu.
- Jangan sembunyikan informasi penting di dalam warna saja (tanpa ikon atau teks).
- Jangan gunakan opacity rendah sebagai pengganti state disabled yang proper.
- Jangan gunakan font selain Plus Jakarta Sans (display) dan Geist Sans (body) tanpa alasan kuat.

---

## 16. Catatan Implementasi Laravel + TailwindCSS 4

### Blade Components

Rekomendasikan struktur Blade component yang mencerminkan token desain:

```
resources/views/components/
├── queue/
│   ├── ticket-card.blade.php      # queue-ticket-card
│   ├── monitor.blade.php          # queue-monitor
│   ├── counter-card.blade.php     # queue-counter-card
│   └── status-badge.blade.php     # status-badge
├── service/
│   └── card.blade.php             # service-card
├── ui/
│   ├── button.blade.php           # semua varian button
│   ├── input.blade.php            # text-input
│   ├── modal.blade.php            # modal
│   └── toast.blade.php            # toast
└── layout/
    ├── nav.blade.php              # top-nav
    ├── sidebar.blade.php          # sidebar petugas
    └── footer.blade.php           # footer
```

### Livewire untuk Real-time

Gunakan **Laravel Livewire** untuk:
- `QueueMonitor` — polling setiap 5 detik untuk update nomor antrean
- `QueueStatus` — status nomor antrean pengguna real-time
- `TicketForm` — form ambil nomor dengan validasi instant

### AlpineJS untuk Interaksi UI

Gunakan **AlpineJS** (sudah bundled dengan Livewire) untuk:
- Toggle modal
- Toast notifications
- Animasi transisi nomor di monitor
- Hamburger menu mobile

---

*Dokumen ini adalah living document. Perbarui setiap kali ada penambahan komponen baru atau perubahan token desain.*

---

**Versi:** 1.0.0  
**Terakhir diperbarui:** 2026  
**Dibuat untuk:** Sistem Manajemen Antrean Digital MPP Kota Sawahlunto  
**Stack:** Laravel 13 · TailwindCSS 4 · Livewire · AlpineJS