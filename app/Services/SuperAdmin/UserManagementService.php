<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Data\SuperAdmin\UserData;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Queue;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementService
{
    /**
     * Ambil data paginasi pengguna beserta metrik untuk halaman index.
     */
    public function getUsersIndexData(Request $request): array
    {
        // ── Metrics ──────────────────────────────────────────────
        $totalUsers = User::count('*');

        $activeStaff = User::online()
            ->whereIn('role', array_column(UserRole::staffRoles(), 'value'))
            ->count();

        $totalInstansi = User::whereNotNull('department_id')
            ->distinct('department_id')
            ->count('department_id');

        // ── Build Query dengan Filter ────────────────────────────
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departments_id')) {
            $query->where('department_id', $request->departments_id);
        } elseif ($request->filled('instansi')) {
            $query->where('department_id', $request->instansi);
        }

        if ($request->filled('role') && in_array($request->role, UserRole::values(), true)) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $users = $query->paginate(10)->withQueryString();

        return [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeStaff' => $activeStaff,
            'totalInstansi' => $totalInstansi,
        ];
    }

    /**
     * Buat user baru dalam sistem.
     */
    public function createUser(UserData $dto): User
    {
        $userData = $dto->toArray();
        $userData['password'] = Hash::make((string) $dto->password);
        $userData['is_active'] = true;

        $user = User::create($userData);

        AuditLogger::userCreated($user);

        return $user;
    }

    /**
     * Perbarui data user.
     */
    public function updateUser(User $user, UserData $dto): User
    {
        // Snapshot data sebelum perubahan untuk audit trail
        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->value,
            'departments_id' => $user->department_id,
            'nomor_loket' => $user->nomor_loket,
            'no_telp' => $user->no_telp,
            'nik' => $user->nik,
        ];

        $user->update($dto->toArray());

        $after = [
            'name' => $user->fresh()->name,
            'email' => $user->fresh()->email,
            'role' => $user->fresh()->role?->value,
            'departments_id' => $user->fresh()->department_id,
            'nomor_loket' => $user->fresh()->nomor_loket,
            'no_telp' => $user->fresh()->no_telp,
            'nik' => $user->fresh()->nik,
        ];

        AuditLogger::userUpdated($user, $before, $after);

        return $user;
    }

    /**
     * Toggle status aktif/nonaktif akun.
     */
    public function toggleStatus(User $user): string
    {
        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::statusToggled($user, (bool) $user->fresh()->is_active);

        return $user->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';
    }

    /**
     * Reset password dengan password sementara yang acak dan aman.
     */
    public function resetPassword(User $user): array
    {
        $tempPassword = Str::password(12, letters: true, numbers: true, symbols: false);

        $user->update(['password' => Hash::make($tempPassword)]);

        AuditLogger::passwordReset($user);

        return [
            'user' => $user->name,
            'password' => $tempPassword,
        ];
    }

    /**
     * Hapus user dari sistem jika tidak ada transaksi antrean aktif.
     */
    public function deleteUser(User $user): array
    {
        // Cek apakah user memiliki Booking/Antrean Aktif (Booked, Checked-In, Serving)
        $hasActive = Queue::where('user_id', $user->id)
            ->whereIn('status', ['Booked', 'Checked-In', 'Serving'])
            ->exists();

        if ($hasActive) {
            return [
                'success' => false,
                'message' => 'Gagal! Akun sedang aktif di antrean atau memiliki booking aktif.',
            ];
        }

        // Log sebelum dihapus agar snapshot tetap tersedia
        AuditLogger::userDeleted($user);

        $name = $user->name;
        $user->delete();

        return [
            'success' => true,
            'message' => "Pengguna {$name} berhasil dihapus dari sistem.",
        ];
    }

    /**
     * Ambil log aktivitas untuk seorang user.
     */
    public function getUserActivityLogs(User $user): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->where(function ($q) use ($user) {
                $q->where('causer_id', $user->id)
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('subject_type', User::class)
                            ->where('subject_id', $user->id);
                    });
            })
            ->with('causer')
            ->latest('created_at')
            ->paginate(20);
    }
}
