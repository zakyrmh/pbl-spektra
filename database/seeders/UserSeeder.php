<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@mpp-sawahlunto.id'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        // Admin Front Office
        User::updateOrCreate(
            ['email' => 'fo@mpp-sawahlunto.id'],
            [
                'name' => 'Petugas Front Office',
                'email' => 'fo@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'admin_fo',
            ]
        );

        $counter = Counter::where('name', 'like', '%Loket 01%')->first();

        // Admin Gerai
        User::updateOrCreate(
            ['email' => 'gerai@mpp-sawahlunto.id'],
            [
                'name' => 'Petugas Gerai',
                'email' => 'gerai@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'admin_gerai',
                'counter_id' => $counter ? $counter->id : null,
            ]
        );

        // Pengunjung (NIK dan phone_number wajib diisi)
        User::updateOrCreate(
            ['email' => 'pengunjung@example.com'],
            [
                'name' => 'Budi Santoso',
                'nik' => '1372010101900001',
                'email' => 'pengunjung@example.com',
                'phone_number' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'pengunjung',
            ]
        );

        $this->command->info('✅ User seeder berhasil! Akun tersedia:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['super_admin', 'superadmin@mpp-sawahlunto.id', 'password'],
                ['admin_fo',    'fo@mpp-sawahlunto.id',         'password'],
                ['admin_gerai', 'gerai@mpp-sawahlunto.id',      'password'],
                ['pengunjung',  'pengunjung@example.com',        'password'],
            ]
        );
    }
}
