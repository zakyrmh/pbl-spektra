# 📖 Mini Documentation: PHP 8.5 untuk Laravel 13 Development

Dokumen ini berisi rangkuman fitur-fitur utama, sintaks baru, serta penyesuaian (_deprecations_) pada **PHP 8.5** yang dapat dimanfaatkan secara optimal dalam proyek berbasis **Laravel 13**.

---

## 🚀 Fitur Utama & Sintaks Baru PHP 8.5

### 1. Pipe Operator (`|>`)

Memungkinkan pemanggilan _callable_ atau fungsi secara berantai (_chaining_) dari kiri ke kanan. Fitur ini menghilangkan ketergantungan pada pemanggilan fungsi bersarang (_nested function calls_) atau variabel perantara (_intermediate variables_).

- **Penggunaan pada Laravel:** Sangat bermanfaat saat mengolah koleksi data, DTO, atau pemrosesan _pipeline_ data tanpa merusak keterbacaan kode.

```php
// Sebelum PHP 8.5 (Nested Calls)
$formatted = strtoupper(trim(slugify($title)));

// PHP 8.5 (Pipe Operator)
$formatted = $title
    |> 'trim'
    |> 'slugify'
    |> 'strtoupper';

```

---

### 2. Modifikasi Objek Saat Kloning (`clone with`)

PHP 8.5 memperkenalkan ekstensi sintaks `clone` untuk langsung memperbarui properti objek saat proses kloning.

- **Penggunaan pada Laravel:** Sangat ideal untuk **Readonly Classes** (seperti Data Transfer Objects / DTOs atau Value Objects) ketika menerapkan _wither pattern_ (pola Immutability).

```php
// DTO Readonly di Laravel 13
readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role
    ) {}
}

$user = new UserData('Ahmad', 'ahmad@example.com', 'editor');

// PHP 8.5: Memperbarui properti 'role' secara immutably
$admin = clone $user with ['role' => 'admin'];

```

---

### 3. Attribute `#[NoDiscard]`

Atribut `#[\NoDiscard]` memberikan peringatan (_warning_) dari engine jika nilai pengembalian (_return value_) dari suatu fungsi atau method tidak digunakan/ditangkap oleh pemanggil.

- **Penggunaan pada Laravel:** Mencegah _silent bug_ pada method Service / Action layer yang mengembalikan status penting, objek transaksi, atau DTO baru.

```php
namespace App\Services;

class PaymentService
{
    #[\NoDiscard]
    public function processTransaction(Order $order): TransactionResult
    {
        // Logika pemrosesan pembayaran...
        return new TransactionResult(success: true, reference: $ref);
    }
}

// Di Controller / Action:
$service = new PaymentService();

// Error/Warning: Return value ditolak jika tidak disimpan
$service->processTransaction($order);

// Benar:
$result = $service->processTransaction($order);

```

---

### 4. Ekstensi URI Bawaan (_Built-in URI Extension_)

PHP 8.5 menambahkan ekstensi URI bawaan yang mematuhi standar RFC 3986 dan WHATWG URL.

- **Penggunaan pada Laravel:** Memberikan pemrosesan dan manipulasi URL/URI tingkat lanjut secara native dengan performa lebih cepat sebelum atau bersamaan dengan penggunaan helper `Illuminate\Support\Uri` milik Laravel.

```php
use Uri\Uri;

$uri = Uri::parse('https://example.com/api/v1/users?page=2');

echo $uri->getHost(); // example.com
echo $uri->getPath(); // /api/v1/users

```

---

### 5. Fungsi Helpers Array Baru: `array_first()` & `array_last()`

PHP 8.5 menyediakan fungsi bawaan `array_first()` dan `array_last()` untuk mengambil elemen pertama atau terakhir dari array.

- **Penggunaan pada Laravel:** Melengkapi helper bawaan PHP ketika bekerja langsung dengan _plain array_ tanpa perlu membuat instans `Illuminate\Support\Collection`.

```php
$data = ['apple', 'banana', 'cherry'];

$first = array_first($data); // 'apple'
$last  = array_last($data);  // 'cherry'

```

---

### 6. Closure & First-Class Callables pada Constant Expressions

Static closures dan _first-class callables_ sekarang dapat digunakan dalam ekspresi konstan, seperti parameter atribut.

```php
class UserRequest extends FormRequest
{
    // Menggunakan closure statis atau callable dalam atribut
    #[\App\Attributes\CustomRule(fn($value) => $value > 10)]
    public int $amount;
}

```

---

## ⚠️ Perubahan Penting & Deprecations (BC Breaks)

Saat bermigrasi atau mengembangkan aplikasi berbasis Laravel 13 di PHP 8.5, perhatikan beberapa fitur yang telah ditandai sebagai _deprecated_:

1. **Backtick Operator Deprecated:**

- Penggunaan backtick (`ls -la`) sebagai alias `shell_exec()` telah di-deprecated. Gunakan `Illuminate\Support\Facades\Process` bawaan Laravel atau fungsi `shell_exec()` langsung.

2. **Method Magic `__sleep()` dan `__wakeup()` Soft-Deprecated:**

- Gunakan `__serialize()` dan `__unserialize()` jika membuat kustomisasi serialisasi pada Eloquent Model atau DTO.

3. **Pengecoran Tipe Data Non-Standar Ditindak:**

- Penggunaan `(boolean)`, `(integer)`, `(double)`, dan `(binary)` di-deprecated. Selalu gunakan tipe standar: `(bool)`, `(int)`, `(float)`, dan `(string)`.

4. **Semicolon pada Skenario `switch-case`:**

- Penulisan `case 'value';` (menggunakan titik koma) di-deprecated. Gunakan titik dua `case 'value':`.

5. **Penggunaan `null` sebagai Array Offset:**

- Memakai `null` sebagai index array (seperti `$arr[null]`) atau pada `array_key_exists()` akan memicu warning.

---

## 🛠️ Rekomendasi Setup & Environment untuk Laravel 13

1. **Check php.ini Extensions:** Pastikan ekstensi `uri` dan `curl` diaktifkan.
2. **Composer Setup:** Pada file `composer.json`, atur batasan PHP untuk memastikan proyek Anda aman saat di-deploy:

```json
"require": {
    "php": "^8.5",
    "laravel/framework": "^13.0"
}

```

3. **Linter / Static Analysis:** Perbarui PHPStan / Pest PHP ke versi terbaru agar mengenali sintaks `|>`, `clone with`, dan atribut `#[\NoDiscard]`.
