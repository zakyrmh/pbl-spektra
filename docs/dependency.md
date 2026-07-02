# Identifikasi Dependensi Proyek (PBL Spektra)

Dokumen ini memuat daftar pustaka (dependency/package) yang digunakan dalam proyek **PBL Spektra**. Seluruh data disinkronkan secara langsung dengan berkas konfigurasi `composer.json`, `composer.lock`, `package.json`, dan `package-lock.json` untuk memastikan akurasi versi yang terpasang di lingkungan pengembangan.

---

## 📊 1. Dependensi Backend (PHP / Composer)

Berikut adalah daftar pustaka PHP yang dikelola menggunakan **Composer**, baik untuk kebutuhan runtime aplikasi utama maupun kebutuhan lingkungan pengembangan (development & testing):

| Package | Tipe | Versi (Constraint) | Versi Terkunci (Locked) | Fungsi Utama |
| :--- | :---: | :---: | :---: | :--- |
| **[php](https://www.php.net/)** | Runtime | `^8.3` | N/A | Lingkungan runtime dasar aplikasi PHP. |
| **[laravel/framework](https://packagist.org/packages/laravel/framework)** | Core | `^13.0` | `13.17.0` | Framework inti PHP Laravel (routing, Eloquent ORM, middleware, Blade, dll). |
| **[laravel/tinker](https://packagist.org/packages/laravel/tinker)** | Core | `^3.0` | `3.0.2` | REPL (Read-Eval-Print Loop) interaktif Laravel di command line. |
| **[fakerphp/faker](https://packagist.org/packages/fakerphp/faker)** | Dev | `^1.23` | `1.24.1` | Pustaka untuk generate data palsu (faker) untuk seeder & factory. |
| **[laravel/boost](https://packagist.org/packages/laravel/boost)** | Dev | `^2.2` | `2.4.10` | Utilitas AI-assisted development dan generator boilerplate code. |
| **[laravel/pail](https://packagist.org/packages/laravel/pail)** | Dev | `^1.2.5` | `1.2.7` | Log tailing real-time langsung melalui terminal. |
| **[laravel/pint](https://packagist.org/packages/laravel/pint)** | Dev | `^1.29` | `1.29.3` | Code formatter PHP berbasis PHP-CS-Fixer dengan gaya Laravel. |
| **[mockery/mockery](https://packagist.org/packages/mockery/mockery)** | Dev | `^1.6` | `1.6.12` | Framework objek mock PHP untuk pembuatan unit testing. |
| **[nunomaduro/collision](https://packagist.org/packages/nunomaduro/collision)** | Dev | `^8.6` | `8.9.4` | Handler error CLI yang interaktif untuk aplikasi konsol PHP. |
| **[pestphp/pest](https://packagist.org/packages/pestphp/pest)** | Dev | `^4.4` | `4.7.4` | Framework testing PHP yang elegan dan minimalis. |
| **[pestphp/pest-plugin-laravel](https://packagist.org/packages/pestphp/pest-plugin-laravel)** | Dev | `^4.1` | `4.1.0` | Integrasi dan helper pengujian spesifik untuk framework Laravel di Pest. |

---

## 💻 2. Dependensi Frontend & Tooling (Node.js / NPM)

Berikut adalah daftar pustaka Javascript/Node.js yang dikelola menggunakan **NPM** di bawah bagian `devDependencies`:

| Package | Tipe | Versi (Constraint) | Versi Terkunci (Locked) | Fungsi Utama |
| :--- | :---: | :---: | :---: | :--- |
| **[@tailwindcss/vite](https://www.npmjs.com/package/@tailwindcss/vite)** | Dev | `^4.0.0` | `4.2.2` | Plugin resmi untuk mengintegrasikan Tailwind CSS v4 dengan Vite bundler. |
| **[axios](https://www.npmjs.com/package/axios)** | Dev | `>=1.11.0 <=1.14.0` | `1.14.0` | HTTP client berbasis Promise untuk komunikasi API asinkron. |
| **[concurrently](https://www.npmjs.com/package/concurrently)** | Dev | `^9.0.1` | `9.2.1` | Utilitas menjalankan beberapa perintah CLI secara bersamaan dalam satu terminal. |
| **[husky](https://www.npmjs.com/package/husky)** | Dev | `^9.1.7` | `9.1.7` | Git hooks manager untuk memicu linting/formatting sebelum melakukan commit. |
| **[laravel-vite-plugin](https://www.npmjs.com/package/laravel-vite-plugin)** | Dev | `^3.0.0` | `3.0.1` | Plugin Vite resmi dari Laravel untuk pemuatan aset (CSS/JS). |
| **[lint-staged](https://www.npmjs.com/package/lint-staged)** | Dev | `^16.4.0` | `16.4.0` | Menjalankan perintah formatter/linter hanya pada file yang masuk ke git staging. |
| **[tailwindcss](https://www.npmjs.com/package/tailwindcss)** | Dev | `^4.0.0` | `4.2.2` | Utility-first CSS framework versi terbaru (v4) untuk menata antarmuka. |
| **[vite](https://www.npmjs.com/package/vite)** | Dev | `^8.0.0` | `8.0.8` | Build tool dan bundler modern berkecepatan tinggi. |

---

## 🛠️ 3. Detail Cara Instalasi & Dampak Penggunaan Dependensi Utama

### A. Dependensi Backend (Composer)

#### 1. laravel/framework
* **Perintah Instalasi**: Terpasang otomatis saat inisialisasi skeleton Laravel.
* **Dampak & Kegunaan**: Menyediakan runtime backend, sistem routing, ORM Eloquent, middleware, Blade template, dan penanganan HTTP request.
* **Risiko**: Kerusakan sistem/error jika terjadi inkonsistensi sintaks saat pembaruan versi major (misalnya migrasi antar versi Laravel).

#### 2. laravel/tinker
* **Perintah Instalasi**: Terpasang otomatis, atau via `composer require laravel/tinker --dev`.
* **Dampak & Kegunaan**: Menyediakan REPL interaktif (`php artisan tinker`) untuk berinteraksi langsung dengan database, testing query, dan model PHP di terminal.
* **Risiko**: Eksekusi perintah yang salah di server produksi (seperti truncate database) dapat merusak data secara permanen.

#### 3. laravel/boost
* **Perintah Instalasi**: `composer require laravel/boost --dev`
* **Dampak & Kegunaan**: Menyediakan code generator bertenaga AI dan utilitas scaffolding untuk mempercepat pembuatan controller, model, dan migration.
* **Risiko**: Over-dependency pada generator otomatis bagi developer baru yang belum terlalu memahami alur dasar scaffolding.

#### 4. laravel/pail
* **Perintah Instalasi**: `composer require laravel/pail --dev`
* **Dampak & Kegunaan**: Memungkinkan pengembang melakukan streaming error log aplikasi secara real-time langsung ke CLI (`php artisan pail`).
* **Risiko**: Relatif aman dan tidak berdampak ke lingkungan produksi karena dikategorikan sebagai dev-only package.

#### 5. laravel/pint
* **Perintah Instalasi**: `composer require laravel/pint --dev`
* **Dampak & Kegunaan**: Merapikan gaya penulisan kode PHP agar seragam menggunakan standar Laravel (`./vendor/bin/pint`).
* **Risiko**: Potensi terjadinya konflik merge yang masif jika format otomatis dijalankan pada file kerja anggota tim lain yang belum dicommit.

#### 6. pestphp/pest & pest-plugin-laravel
* **Perintah Instalasi**: `composer require pestphp/pest pestphp/pest-plugin-laravel --dev`
* **Dampak & Kegunaan**: Menyediakan framework testing modern yang clean dan API yang ramah untuk menguji unit dan fitur aplikasi Laravel.
* **Risiko**: Memerlukan komitmen tim untuk menulis pengujian secara disiplin agar cakupan tes tetap terjaga seiring berkembangnya kode program.

---

### B. Dependensi Frontend (NPM)

#### 1. tailwindcss & @tailwindcss/vite
* **Perintah Instalasi**: Terpasang melalui `npm install tailwindcss @tailwindcss/vite --save-dev`
* **Dampak & Kegunaan**: Menyediakan engine utility-first CSS v4 terbaru yang terintegrasi langsung ke siklus build Vite untuk kompilasi stylesheet yang optimal.
* **Risiko**: Kepatuhan sintaks baru pada Tailwind v4 yang memerlukan penyesuaian dari kebiasaan versi v3 (seperti penghapusan file konfigurasi JavaScript dan beralih ke CSS-first configuration).

#### 2. vite & laravel-vite-plugin
* **Perintah Instalasi**: Terpasang melalui `npm install vite laravel-vite-plugin --save-dev`
* **Dampak & Kegunaan**: Mengompilasi serta memuat aset CSS dan JS secara dinamis (HMR - Hot Module Replacement) selama masa pengembangan dan kompilasi produksi.
* **Risiko**: Eror kompilasi aset jika konfigurasi path di file `vite.config.js` tidak sesuai dengan struktur build server.

#### 3. axios
* **Perintah Instalasi**: `npm install axios --save-dev`
* **Dampak & Kegunaan**: Membantu melakukan pengiriman request asynchronous (AJAX) dari antarmuka (frontend) ke API backend Laravel.
* **Risiko**: Kerentanan keamanan CORS jika API Laravel tidak dikonfigurasi dengan aman, serta penanganan error promise yang tidak terkelola dengan baik di sisi klien.

#### 4. husky & lint-staged
* **Perintah Instalasi**: `npm install husky lint-staged --save-dev`
* **Dampak & Kegunaan**: Mengotomatiskan eksekusi formatter (`laravel/pint`) hanya pada file PHP yang diubah tepat sesaat sebelum kode di-commit ke Git.
* **Risiko**: Proses commit bisa sedikit melambat sesaat karena sistem harus menjalankan evaluasi linter/formatter terlebih dahulu.
