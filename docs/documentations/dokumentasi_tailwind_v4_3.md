# 📖 Mini Documentation: Tailwind CSS v4.3 untuk Developer Modern

Dokumen ini berisi rangkuman fitur-fitur penting, perubahan arsitektural utama di **Tailwind CSS v4**, serta pembaruan spesifik hingga versi **v4.3** untuk meningkatkan efisiensi _styling_ proyek web Anda (seperti Next.js, SvelteKit, atau Laravel).

---

## 🚀 Perubahan Utama & Arsitektur v4.x

Tailwind CSS v4 mengalami perombakan besar-besaran (_engine rewrite_) menggunakan **Rust** yang menghasilkan waktu _build_ hingga **10x lebih cepat**.

### 1. Konfigurasi Berbasis CSS Pertama (`@theme`)

Di v4, file `tailwind.config.js` tidak lagi digunakan secara _default_. Seluruh konfigurasi tema, warna, font, dan kustomisasi dilakukan **langsung di dalam file CSS utama** Anda menggunakan arahan `@theme`.

```css
/* main.css / app.css */
@import "tailwindcss";

@theme {
    --font-display: "Satoshi", "Inter", sans-serif;
    --color-brand: #3b82f6;
    --color-brand-dark: #1d4ed8;
    --breakpoint-3xl: 120rem;
}
```

---

### 2. Impor Tunggal & Pembersihan Directives

Anda tidak lagi memerlukan `@tailwind base;`, `@tailwind components;`, dan `@tailwind utilities;`. Cukup gunakan satu baris `@import`:

```css
@import "tailwindcss";
```

---

### 3. Native CSS Variables & Color Functions

Seluruh nilai warna dan konfigurasi di-generate menjadi variabel CSS native (`var(--color-*)`). Hal ini mempermudah manipulasi _theme_ secara dinamis di runtime via JavaScript/TypeScript.

```html
<!-- Mengakses variabel CSS kustom langsung via utility class -->
<div class="bg-brand text-white p-4 rounded-xl shadow-md">
    Halo dari Tailwind v4!
</div>
```

---

## ✨ Fitur Baru & Highlight di Tailwind CSS v4.3

---

### 1. Pembaruan Container Queries Kustom

Tailwind v4 mempermudah penerapan _Container Queries_ tanpa memerlukan plugin tambahan. Pada v4.3, dukungan sintaks kustom dan varian turunan makin diperhalus.

```html
<!-- Menandai elemen sebagai container -->
<div class="@container">
    <!-- Menerapkan style berdasarkan ukuran container (bukan viewport) -->
    <div class="grid grid-cols-1 @sm:grid-cols-2 @lg:grid-cols-3 gap-4">
        <div class="p-4 bg-gray-100 rounded">Item 1</div>
        <div class="p-4 bg-gray-100 rounded">Item 2</div>
    </div>
</div>
```

---

### 2. Modern CSS Color Notation & P3 Color Spaces

Tailwind v4.3 memanfaatkan ruang warna konsol generasi baru (_wide-gamut OKLCH / Display P3_) secara default untuk warna yang lebih cerah dan akurat pada layar modern.

```html
<!-- Mengatur opasitas dengan sintaks modern / (slash) -->
<div class="bg-brand/80 text-black/90 hover:bg-brand/100 transition-colors">
    Tombol dengan OKLCH Alpha Channel
</div>
```

---

### 3. Dynamic Utilities & Arbitrary Values yang Lebih Fleksibel

Di v4.3, penulisan _arbitrary values_ (nilai kustom) menjadi lebih intuitif tanpa perlu menyertakan ekstensi fungsi spesifik secara manual.

```html
<!-- Arbitrary properties & values -->
<div
    class="[clip-path:polygon(0_0,_100%_0,_100%_100%,_0_80%)] bg-brand text-white p-8"
>
    Custom Clip Path
</div>

<!-- Dynamic Grid & Gap -->
<div class="grid grid-cols-[repeat(auto-fit,minmax(250px,1fr))] gap-4">
    <div>Card A</div>
    <div>Card B</div>
</div>
```

---

### 4. Peningkatan Varian State & Pseudo-Classes

Varian seperti `:has()`, `:is()`, dan `:not()` mendapat optimasi performa _parser_ pada v4.3.

```html
<!-- Mengubah gaya parent jika input di dalamnya aktif/fokus -->
<form
    class="p-4 border rounded-lg has-[:focus]:border-brand has-[:focus]:ring-2"
>
    <input
        type="text"
        class="outline-none w-full"
        placeholder="Ketik sesuatu..."
    />
</form>
```

---

### 5. Dukungan `@utility` Directive

Jika Anda ingin membuat _utility class_ kustom yang dapat memanfaatkan fitur Tailwind (seperti varian `hover:`, `md:`, `dark:`), gunakan `@utility`:

```css
@import "tailwindcss";

@utility btn-primary {
    background-color: var(--color-brand);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background-color 0.2s ease;
}
```

```html
<!-- Penggunaan di HTML -->
<button class="btn-primary hover:bg-brand-dark">Submit</button>
```

---

## ⚠️ Lembar Migrasi Singkat (Cheat Sheet v3 -> v4)

| Kebutuhan / Fitur          | Tailwind v3                      | Tailwind v4.3                      |
| :------------------------- | :------------------------------- | :--------------------------------- |
| **Konfigurasi Utama**      | `tailwind.config.js`             | `@theme` di file CSS utama         |
| **Import CSS**             | `@tailwind base;` dll.           | `@import "tailwindcss";`           |
| **Plugin Container Query** | `@tailwindcss/container-queries` | Sudah Bawaan Native (`@container`) |
| **Variabel Warna**         | RGB / HSL                        | OKLCH (Wide Gamut P3)              |
| **Custom Utility**         | `@layer utilities { ... }`       | `@utility my-class { ... }`        |
| **Build Engine**           | PostCSS / JS                     | Rust Oxide Engine                  |

---

## 🛠️ Setup Cepat di Proyek Modern

1. **Instalasi Package:**
    ```bash
    npm install tailwindcss@next @tailwindcss/vite
    ```
2. **Konfigurasi Vite (`vite.config.ts`):**

    ```ts
    import { defineConfig } from "vite";
    import tailwindcss from "@tailwindcss/vite";

    export default defineConfig({
        plugins: [tailwindcss()],
    });
    ```

3. **Import di CSS (`src/app.css`):**
    ```css
    @import "tailwindcss";
    ```
