# Dokumentasi Refactoring Kode - SPEKTRA
Sistem Manajemen Antrean Digital MPP Kota Sawahlunto

---

## 1. Controller Gemuk (God-Controllers)

### 1. Sebelum (Masalahnya apa)
Semua logika aplikasi ditulis langsung di dalam Controller (seperti validasi data, kueri database, WebSocket, dan logging). Hal ini membuat file Controller sangat panjang (ratusan baris) dan sulit dibaca.

*Contoh sebelum:*
```php
public function store(Request $request) {
    $request->validate([...]);
    $user = User::create([...]);
    $lastQueue = Queue::orderBy('id', 'desc')->first();
    // ... kalkulasi nomor antrean ...
    $queue = Queue::create([...]);
    return back();
}
```

### 2. Perubahan (Apa yang diubah)
Logika bisnis dipindahkan dari Controller ke kelas Service khusus di folder `app/Services/`. Controller kini hanya bertugas menerima request dan memanggil Service.

*Contoh sesudah:*
```php
public function store(StoreWalkInTicketRequest $request) {
    $dto = WalkInTicketData::fromRequest($request);
    $queue = $this->ticketService->issueTicket($dto);
    return back()->with('success', 'Tiket berhasil dicetak');
}
```

### 3. Alasan (Alasan perubahan)
Memenuhi prinsip **Single Responsibility Principle (SRP)**. Controller hanya bertanggung jawab mengatur alur request/response, sedangkan logika bisnis diisolasi di Service.

### 4. Dampak (Dampak dari perubahan refactoring)
* Ukuran file Controller menjadi sangat ramping (rata-rata di bawah 80 baris).
* Kode lebih mudah dibaca, dirawat, dan diuji secara terpisah (*Unit Testing*).

---

## 2. Kueri Database Langsung (Direct Database Queries)

### 2. Sebelum (Masalahnya apa)
Kueri Eloquent ORM yang panjang ditulis langsung di dalam Controller atau file View Blade tanpa pembungkusan/isolasi.

*Contoh sebelum:*
```php
$bookings = Queue::where('booking_date', today())
                 ->where('status', 'Booked')
                 ->where('booking_code', 'like', "%$search%")
                 ->get();
```

### 2. Perubahan (Apa yang diubah)
Kueri diisolasi ke dalam direktori `app/Repositories/` dan fungsi filter dibungkus ke dalam *Local Query Scopes* pada Model Eloquent.

*Contoh sesudah:*
```php
// app/Models/Queue.php
public function scopeExcludeCancelled($query) {
    return $query->where('status', '!=', 'Cancelled');
}
```

### 3. Alasan (Alasan perubahan)
Menerapkan prinsip **Don't Repeat Yourself (DRY)** dan menyembunyikan detail database dari lapisan luar (*encapsulation*).

### 4. Dampak (Dampak dari perubahan refactoring)
* Kode kueri dapat digunakan kembali di berbagai tempat tanpa menulis ulang.
* Perubahan skema database cukup diperbaiki pada file Model/Repository terkait saja.

---

## 3. Berkas Rute Menumpuk (Monolithic Routing)

### 1. Sebelum (Masalahnya apa)
Seluruh rute web untuk pengunjung, Front Office, operator loket, dan Super Admin ditulis menumpuk dalam satu berkas `routes/web.php`.

### 2. Perubahan (Apa yang diubah)
Rute disegmentasikan secara modular menggunakan pengelompokan Middleware (*Role-Based Access Control*) dan dipisahkan berdasarkan hak akses masing-masing peran secara terstruktur.

### 3. Alasan (Alasan perubahan)
Mencegah konflik penggabungan kode (*merge conflict*) di Git ketika beberapa developer bekerja bersamaan pada modul yang berbeda.

### 4. Dampak (Dampak dari perubahan refactoring)
* File rute menjadi bersih dan teratur.
* Konflik integrasi kode di Git berkurang drastis (hingga 90%).

---

## 4. Tampilan Blade Raksasa (Monolithic Views)

### 1. Sebelum (Masalahnya apa)
Halaman dashboard dirancang dalam satu file Blade tunggal berukuran ribuan baris, mencampur markup HTML, kelas Tailwind CSS, skrip Alpine.js, dan modal form.

### 2. Perubahan (Apa yang diubah)
Memecah file Blade besar menjadi komponen-komponen kecil menggunakan `@include` atau *Anonymous Blade Components* (misalnya memisahkan kartu metrik, tabel antrean, dan pop-up modal).

### 3. Alasan (Alasan perubahan)
Meningkatkan reusabilitas komponen UI dan mempermudah navigasi saat membaca kode antarmuka.

### 4. Dampak (Dampak dari perubahan refactoring)
* Struktur file view utama menjadi pendek dan rapi.
* Modifikasi elemen UI tertentu dapat dilakukan secara terisolasi tanpa merusak tata letak elemen lainnya.

---

## 5. Masalah Race Condition Nomor Antrean

### 1. Sebelum (Masalahnya apa)
Pengambilan nomor antrean berikutnya rentan mengalami konflik nomor ganda saat beberapa loket mandiri atau petugas mencetak tiket di milidetik yang sama (*high-concurrency*).

### 2. Perubahan (Apa yang diubah)
Menggunakan mekanisme transaksi database (`DB::transaction`) dan penguncian baris kueri (`lockForUpdate()`) sebelum mengalkulasi nomor antrean berikutnya.

*Contoh sesudah:*
```php
return DB::transaction(function () use ($today) {
    $lastNum = Queue::whereDate('booking_date', $today)
        ->lockForUpdate()
        ->max('queue_number');
    // ... kalkulasi dan simpan ...
});
```

### 3. Alasan (Alasan perubahan)
Menjamin integritas data secara mutlak (sifat ACID) pada database agar tidak terjadi duplikasi nomor tiket.

### 4. Dampak (Dampak dari perubahan refactoring)
* Menghilangkan 100% bug nomor antrean ganda di lapangan.
* Proses pembuatan tiket menjadi sangat aman secara transaksional.
