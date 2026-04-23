<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@mpp-sawahlunto.id'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Akun Petugas
        User::updateOrCreate(
            ['email' => 'petugas@mpp-sawahlunto.id'],
            [
                'name' => 'Petugas Loket 1',
                'username' => 'petugas1',
                'email' => 'petugas@mpp-sawahlunto.id',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]
        );

        $this->command->info('✅ User seeder berhasil! Akun tersedia:');
        $this->command->table(
            ['Role', 'Username', 'Email', 'Password'],
            [
                ['Admin',   'admin',    'admin@mpp-sawahlunto.id',   'password'],
                ['Petugas', 'petugas1', 'petugas@mpp-sawahlunto.id', 'password'],
            ]
        );
    }
}
