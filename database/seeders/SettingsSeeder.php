<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'Mal Pelayanan Publik Sawahlunto',
                'description' => 'Nama resmi aplikasi sistem manajemen antrean.',
            ],
            [
                'key' => 'app_logo',
                'value' => 'images/Logo Mal Pelayanan Publik Kota Sawahlunto.webp',
                'description' => 'Path berkas logo aplikasi yang ditampilkan pada halaman.',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'description' => 'Status mode pemeliharaan sistem (0 = Aktif, 1 = Pemeliharaan).',
            ],
            [
                'key' => 'marquee_text',
                'value' => 'Selamat Datang di Mal Pelayanan Publik Kota Sawahlunto. Budayakan Antre demi Kenyamanan Bersama.',
                'description' => 'Teks berjalan (marquee) yang ditampilkan pada layar monitor publik.',
            ],
            [
                'key' => 'marquee_active',
                'value' => '1',
                'description' => 'Status keaktifan teks berjalan marquee di monitor publik (1 = Tampil, 0 = Sembunyikan).',
            ],
            [
                'key' => 'reverb_host',
                'value' => '127.0.0.1',
                'description' => 'Host WebSocket (Laravel Reverb / Pusher connection).',
            ],
            [
                'key' => 'reverb_port',
                'value' => '8080',
                'description' => 'Port server WebSocket.',
            ],
            [
                'key' => 'reverb_scheme',
                'value' => 'http',
                'description' => 'Protokol server WebSocket (http atau https).',
            ],
            [
                'key' => 'websocket_enabled',
                'value' => '1',
                'description' => 'Aktifkan fitur sinkronisasi real-time antrean via WebSocket (1 = Aktif, 0 = Nonaktif).',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
            // Bersihkan cache terkait
            Cache::forget("setting.{$setting['key']}");
        }
    }
}
