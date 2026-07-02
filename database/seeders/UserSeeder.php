<?php

namespace Database\Seeders;

use App\Models\Department;
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
                'nik' => '1111111111111111',
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
                'nik' => '2222222222222222',
                'email' => 'fo@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'admin_fo',
            ]
        );

        // Cari departemen Bank Nagari secara langsung untuk dicantolkan ke Petugas Gerai
        $department = Department::where('inisial', 'BNR')->first();

        // Admin Gerai
        User::updateOrCreate(
            ['email' => 'gerai@mpp-sawahlunto.id'],
            [
                'name' => 'Petugas Gerai Bank Nagari',
                'nik' => '3333333333333333',
                'email' => 'gerai@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'admin_gerai',
                'department_id' => $department ? $department->id : null,
            ]
        );

        // Pengunjung (Sesuaikan nama kolom ke 'no_telp')
        User::updateOrCreate(
            ['email' => 'pengunjung@example.com'],
            [
                'name' => 'Budi Santoso',
                'nik' => '1372010101900001',
                'email' => 'pengunjung@example.com',
                'no_telp' => '081234567890',
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
