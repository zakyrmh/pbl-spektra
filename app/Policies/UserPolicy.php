<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * Policy otorisasi untuk semua aksi pada model User.
 *
 * Semua method policy menerima $actor (user yang sedang login)
 * dan $target (user yang menjadi objek aksi).
 *
 * Aturan umum:
 * - Hanya SuperAdmin yang dapat mengakses manajemen pengguna.
 * - SuperAdmin TIDAK bisa melakukan aksi destruktif pada dirinya sendiri.
 * - SuperAdmin TIDAK bisa menghapus atau mereset password sesama SuperAdmin lain
 *   (kecuali akun tersebut sudah nonaktif — untuk mencegah eskalasi privilege).
 */
class UserPolicy
{
    /**
     * Tampilkan daftar semua pengguna.
     * Hanya SuperAdmin yang dapat mengakses halaman ini.
     */
    public function viewAny(User $actor): bool
    {
        return $actor->role === UserRole::SuperAdmin;
    }

    /**
     * Lihat detail satu pengguna.
     */
    public function view(User $actor, User $target): bool
    {
        return $actor->role === UserRole::SuperAdmin;
    }

    /**
     * Buat pengguna baru.
     */
    public function create(User $actor): bool
    {
        return $actor->role === UserRole::SuperAdmin;
    }

    /**
     * Perbarui data pengguna.
     *
     * SuperAdmin tidak bisa mengubah peran sesama SuperAdmin lain
     * (mencegah privilege escalation antar admin).
     */
    public function update(User $actor, User $target): bool
    {
        if ($actor->role !== UserRole::SuperAdmin) {
            return false;
        }

        // Tidak boleh ubah data SuperAdmin lain (kecuali dirinya sendiri)
        if ($target->role === UserRole::SuperAdmin && $actor->id !== $target->id) {
            return false;
        }

        return true;
    }

    /**
     * Toggle status aktif/nonaktif akun.
     *
     * SuperAdmin tidak bisa menonaktifkan dirinya sendiri.
     */
    public function toggleStatus(User $actor, User $target): bool
    {
        if ($actor->role !== UserRole::SuperAdmin) {
            return false;
        }

        return $actor->id !== $target->id;
    }

    /**
     * Reset password pengguna.
     *
     * SuperAdmin tidak bisa mereset passwordnya sendiri melalui fitur ini
     * (harus via halaman profil/forgot password).
     * Juga tidak bisa mereset password sesama SuperAdmin.
     */
    public function resetPassword(User $actor, User $target): bool
    {
        if ($actor->role !== UserRole::SuperAdmin) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        // Tidak bisa reset password sesama SuperAdmin
        if ($target->role === UserRole::SuperAdmin) {
            return false;
        }

        return true;
    }

    /**
     * Hapus pengguna dari sistem.
     *
     * Tidak bisa hapus diri sendiri, dan tidak bisa hapus sesama SuperAdmin.
     */
    public function delete(User $actor, User $target): bool
    {
        if ($actor->role !== UserRole::SuperAdmin) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        // Tidak bisa hapus sesama SuperAdmin
        if ($target->role === UserRole::SuperAdmin) {
            return false;
        }

        return true;
    }

    /**
     * Lihat log aktivitas seorang pengguna.
     */
    public function viewActivityLog(User $actor, User $target): bool
    {
        return $actor->role === UserRole::SuperAdmin;
    }

    /**
     * Kelola sesi aktif pengguna (lihat dan revoke).
     */
    public function manageSessions(User $actor, User $target): bool
    {
        if ($actor->role !== UserRole::SuperAdmin) {
            return false;
        }

        // Tidak bisa revoke sesi diri sendiri melalui fitur ini
        return $actor->id !== $target->id;
    }
}
