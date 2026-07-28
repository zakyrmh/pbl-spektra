# Dokumentasi Struktur Folder `app/` pada Laravel 13

Folder `app/` merupakan inti (*heart*) dari aplikasi berbasis **Laravel 13**. Folder ini menampung seluruh logika bisnis, pemrosesan HTTP request, pemodelan data, serta integrasi layanan internal aplikasi.

Sejak Laravel 11 hingga **Laravel 13**, Laravel menerapkan pendekatan **Minimalistic Project Structure**. Folder-folder pendukung di dalam `app/` tidak lagi dibuat secara default saat instalasi awal, melainkan akan dibuat **secara otomatis** (*on-demand*) ketika Anda menjalankan perintah Artisan terkait (misal: `php artisan make:job` atau `php artisan make:request`).

---

## 1. Ringkasan Struktur Folder `app/`

Berikut adalah direktori dan file standar maupun kustom (yang direkomendasikan) di dalam folder `app/`:

```text
app/
├── Actions/                  (Rekomendasi - Action Classes)
├── Console/                  (Perintah CLI kustom)
├── DTOs/                     (Rekomendasi - Data Transfer Objects)
├── Enums/                    (Rekomendasi - PHP Enums)
├── Events/                   (Event listener system)
├── Exceptions/               (Handler pengecualian khusus)
├── Http/                     (Layer HTTP Handling)
│   ├── Controllers/          (Request router & response dispatcher)
│   ├── Middleware/           (Penyaring HTTP Request)
│   └── Requests/             (Form Request Validation)
├── Jobs/                     (Queueable background jobs)
├── Listeners/                (Penangan Event)
├── Models/                   (Eloquent ORM Entities)
├── Notifications/            (Notifikasi multi-channel)
├── Policies/                 (Otorisasi & Hak Akses)
├── Providers/                (Service Providers & Bootstrapping)
│   └── AppServiceProvider.php
├── Rules/                    (Aturan Validasi Kustom)
└── Services/                 (Rekomendasi - Business Logic Services)
```

---

## 2. Penjelasan Detail Setiap Folder

---

### 📂 `app/Http/`
Direktori ini menangani semua hal yang berkaitan dengan **HTTP Request & Response**.

* **`Http/Controllers/`**
  * **Fungsi:** Tempat menyimpan kelas Controller. Controller bertugas menerima masukan (*request*), memanggil logika bisnis, dan mengembalikan luaran (*response/view/JSON*).
  * **Best Practice:** Terapkan prinsip *Skinny Controllers*. Hindari menulis query database rumit atau validasi langsung di dalam controller.
  * **Command:** `php artisan make:controller OrderController`

* **`Http/Requests/`**
  * **Fungsi:** Menyimpan kelas *Form Request* khusus untuk menangani validasi input dan otorisasi pengguna sebelum masuk ke controller.
  * **Best Practice:** Gunakan `$request->validated()` di controller untuk memastikan hanya data terverifikasi yang diproses.
  * **Command:** `php artisan make:request StoreOrderRequest`

* **`Http/Middleware/`**
  * **Fungsi:** Menyimpan middleware kustom untuk menyaring HTTP Request (misal: mengecek autentikasi, meng-header CORS, atau memverifikasi peran pengguna).
  * **Note Laravel 13:** Pendaftaran middleware global/route dilakukan secara bersih melalui file `bootstrap/app.php`.
  * **Command:** `php artisan make:middleware CheckRole`

---

### 📂 `app/Models/`
* **Fungsi:** Tempat menyimpan kelas **Eloquent Model**. Setiap model mewakili satu tabel di dalam database.
* **Tanggung Jawab Utama:**
  * Definisi relasi antar tabel (`hasMany`, `belongsTo`, dll).
  * Penentuan tipe data atribut (*Attribute Casting*).
  * Penulisan *Query Scopes* untuk menyederhanakan query berulang.
* **Best Practice:** Jangan letakkan seluruh logika bisnis kompleks aplikasi di dalam model (*avoid God Models*).
* **Command:** `php artisan make:model Product`

---

### 📂 `app/Providers/`
* **Fungsi:** Tempat melakukan *bootstrapping* dan pendaftaran layanan (*Service Binding*) ke dalam *Laravel Dependency Injection Container*.
* **File Utama (`AppServiceProvider.php`):**
  * Di Laravel 13, `AppServiceProvider.php` menjadi tempat sentral untuk mendaftarkan konfigurasi ringan seperti HTTPS enforcement, Paginator styling, Gate/Policy binding, atau kustomisasi Eloquent.
* **Command:** `php artisan make:provider PaymentServiceProvider`

---

### 📂 `app/Jobs/`
* **Fungsi:** Menyimpan kelas *Queueable Job* yang diproses di latar belakang (*background task*).
* **Penggunaan Umum:** Pengiriman email massal, konversi video, ekspor laporan CSV/Excel berukuran besar, atau pemrosesan pembayaran third-party.
* **Best Practice:** Gunakan interface `ShouldQueue` agar proses tidak memblokir respon HTTP pengguna.
* **Command:** `php artisan make:job ProcessPodcast`

---

### 📂 `app/Events/` & `app/Listeners/`
Dua direktori ini bekerja sama menerapkan pola **Event-Driven Architecture**.

* **`Events/`:** Menyimpan peristiwa yang terjadi di dalam aplikasi (misal: `OrderPlaced`, `UserRegistered`).
* **`Listeners/`:** Menyimpan aksi/reaksi yang merespons peristiwa tersebut (misal: `SendOrderNotification`, `UpdateInventory`).
* **Command:**
  * `php artisan make:event OrderPlaced`
  * `php artisan make:listener SendOrderNotification`

---

### 📂 `app/Policies/`
* **Fungsi:** Menyimpan kelas otorisasi hak akses (*Authorization*) terhadap suatu resource/model tertentu.
* **Penggunaan:** Menentukan apakah seorang user berhak melihat (*view*), membuat (*create*), memperbarui (*update*), atau menghapus (*delete*) data pada model.
* **Command:** `php artisan make:policy PostPolicy --model=Post`

---

### 📂 `app/Notifications/`
* **Fungsi:** Menangani pengiriman notifikasi terpadu ke berbagai channel sekaligus (Email, SMS, Database, Slack, Broadcast, atau Webpush).
* **Command:** `php artisan make:notification InvoicePaid`

---

### 📂 `app/Exceptions/`
* **Fungsi:** Tempat menyimpan kelas *Custom Exception* yang dibuat khusus untuk menangani skenario error spesifik pada aplikasi Anda (misal: `PaymentFailedException`).
* **Command:** `php artisan make:exception PaymentFailedException`

---

### 📂 `app/Rules/`
* **Fungsi:** Menyimpan aturan validasi kustom (*Custom Validation Rules*) yang dapat di-reuse pada Form Request.
* **Command:** `php artisan make:rule Uppercase`

---

### 📂 `app/Console/`
* **Fungsi:** Tempat menyimpan perintah CLI kustom (*Artisan Commands*) buatan Anda sendiri.
* **Command:** `php artisan make:command SendWeeklyReport`

---

## 3. Folder Arsitektur Kustom (Sangat Direkomendasikan)

Untuk menjaga kode tetap bersih, teruji, dan mudah dikembangkan pada proyek skala menengah hingga besar, komunitas Laravel sangat merekomendasikan pembuatan folder kustom berikut di dalam `app/`:

### 📂 `app/Services/`
* **Fungsi:** Menampung kelas *Service* yang berisi logika bisnis utama (*Business Logic Layer*).
* **Tujuan:** Memisahkan logika aplikasi dari controller agar dapat dipanggil dari mana saja (Controller, Job, Artisan Command, atau API).

### 📂 `app/Actions/`
* **Fungsi:** Mengimplementasikan *Single Responsibility Principle* di mana satu kelas hanya menjalankan **satu aksi bisnis spesifik** (misal: `CreateUserAction`, `CancelOrderAction`).

### 📂 `app/DTOs/` (Data Transfer Objects)
* **Fungsi:** Menyimpan struktur data terdefinisi (*strongly-typed*) yang dikirimkan antar layer (misal: dari Controller ke Service) untuk menghindari bug akibat manipulasi array acak.

### 📂 `app/Enums/`
* **Fungsi:** Menyimpan PHP Enums bawaan (sejak PHP 8.1) untuk mendefinisikan nilai konstan (misal: `OrderStatus::PENDING`, `UserRole::ADMIN`).

---

## 4. Matriks Ringkasan & Best Practice

| Folder | Dibuat Otomatis Via Artisan? | Peran / Tanggung Jawab | Best Practice Utama |
| :--- | :---: | :--- | :--- |
| **`Http/Controllers`** | Default | Routing request & response | Jaga tetap singkat (*Thin Controller*). |
| **`Http/Requests`** | Ya | Validasi input user | Selalu pisahkan aturan validasi dari controller. |
| **`Models`** | Default | Pemodelan data Eloquent | Fokus pada relasi, casting, & query scope. |
| **`Providers`** | Default | Service Bootstrapping | Gunakan `AppServiceProvider` untuk pendaftaran global. |
| **`Jobs`** | Ya | Background Tasks (Queue) | Pindahkan pemrosesan berat (>200ms) ke Job. |
| **`Policies`** | Ya | Otorisasi Hak Akses | Gunakan alih-alih kondisi `if ($user->role == 'admin')` langsung di controller. |
| **`Services / Actions`**| Kustom | Logika Bisnis Utama | Buat folder ini jika controller Anda mulai terlalu panjang. |

---
*Dokumentasi ini disusun sesuai dengan standar arsitektur **Laravel 13**.*
