<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'marquee_text'],
            [
                'value' => 'Selamat Datang di Mal Pelayanan Publik (MPP) Kota Sawahlunto. Budayakan Antrean yang Tertib dan Ramah. Jam Operasional Pelayanan: Senin s/d Jumat, Pukul 08:00 - 15:30 WIB.',
                'description' => 'Teks berjalan pada bagian bawah Display Monitor',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'marquee_active'],
            [
                'value' => 'true',
                'description' => 'Status aktif teks berjalan (true/false)',
            ]
        );
    }
}
