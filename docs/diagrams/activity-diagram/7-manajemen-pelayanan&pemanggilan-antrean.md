```mermaid
flowchart TD
    %% DEFINISI STYLE
    classDef startEnd fill:#f9f,stroke:#333,stroke-width:2px;
    classDef proses fill:#bbf,stroke:#333,stroke-width:1px;
    classDef kondisi fill:#ff9,stroke:#333,stroke-width:1px;

    Start([🎯 Start])
    End([🛑 Stop])
    class Start,End startEnd;

    %% STEP 1: LIHAT DATA ANTREAN
    Start --> L1["Admin Counter: Masuk ke Halaman Dashboard Antrean Gerai"]
    L1 --> S1["Sistem: Ambil Data Antrean dari DB Terfilter Berdasarkan ID Counter/Gerai Admin"]

    S1 --> Cond1{Apakah Terdapat\nAntrean Aktif?}:::kondisi
    Cond1 -->|Tidak| S2["Sistem: Tampilkan Pesan 'Belum ada antrean aktif saat ini untuk gerai Anda'"] --> End

    %% TAMPILKAN DAFTAR & DETAIL
    Cond1 -->|Ya| S3["Sistem: Tampilkan Daftar Antrean Berstatus 'Waiting' & 'Serving'"]
    S3 --> L2["Admin Counter: Pilih Salah Satu Nomor Antrean Teratas"]
    L2 --> S4["Sistem: Tampilkan Detail Data Pengunjung & Keperluan Layanan"]

    %% PROSES MEMANGGIL ANTREAN
    S4 --> L3["Admin Counter: Klik Tombol 'Panggil / Mulai Pelayanan'"]
    L3 --> S5["Sistem: Update Status Antrean Menjadi 'Serving'\n& Catat Waktu Mulai (called_at = NOW())"]
    S5 --> S6["Sistem: Trigger Suara Panggilan di Speaker Utama & Update Layar Monitor MPP"]

    %% STEP 2: PROSES MELAYANI & SELESAI
    S6 --> L4["Admin Counter: Memberikan Pelayanan Kepada Pengunjung Fisik"]
    L4 --> L5["Admin Counter: Klik Tombol 'Selesai Dilayani'"]

    %% VALIDASI STATUS BACKEND
    L5 --> S7["Sistem: Jalankan Validasi Backend\n(Periksa Apakah Status Saat ini Benar-benar 'Serving')"]
    S7 --> Cond2{Apakah Status\nValid 'Serving'?}:::kondisi

    %% JALUR ERROR VALIDASI
    Cond2 -->|Tidak| S8["Sistem: Tampilkan Pesan Kesalahan 'Hanya antrean berstatus Serving yang dapat diselesaikan'"]
    S8 --> S3

    %% JALUR VALID - POP-UP KONFIRMASI
    Cond2 -->|Ya| S9["Sistem: Tampilkan Pop-up Konfirmasi 'Apakah pelayanan sudah selesai?'"]
    S9 --> L6["Admin Counter: Pilih Opsi Konfirmasi"]

    %% KEPUTUSAN KONFIRMASI
    L6 --> Cond3{Pilihan\nKonfirmasi?}:::kondisi
    Cond3 -->|Batal| S3

    %% EKSEKUSI SUKSES
    Cond3 -->|Ya, Selesai| S10["Sistem: Update Status Antrean Menjadi 'Completed' di Database"]
    S10 --> S11["Sistem: Catat Waktu Selesai (completed_at = NOW()) & Hitung Total Durasi Pelayanan"]

    %% INTEGRASI EVENT NOTIFIKASI FEEDBACK
    S11 --> S12["Sistem: Trigger Event Listener untuk Mengirim Notifikasi Pengisian Feedback via PWA/Email"]
    S12 --> S13["Sistem: Tampilkan Pesan 'Status antrean berhasil diperbarui' & Refresh Dashboard"]
    S13 --> End
```
