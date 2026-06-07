<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Queue;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Tampilkan halaman Manajemen Pengguna dengan metrics & filter.
     *
     * Gate: viewAny (UserPolicy)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // ── Metrics ──────────────────────────────────────────────
        $totalUsers = User::count('*');
        $activeStaff = User::online()
            ->whereIn('role', array_column(UserRole::staffRoles(), 'value'))
            ->count();
        $totalInstansi = User::whereNotNull('instansi')
            ->distinct('instansi')
            ->count('instansi');

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

        if ($request->filled('instansi')) {
            $query->where('instansi', $request->instansi);
        }

        if ($request->filled('role') && in_array($request->role, UserRole::values())) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $users = $query->paginate(10)->withQueryString();

        if ($request->ajax() || $request->has('ajax') || $request->expectsJson()) {
            return response()->json([
                'html' => view('super_admin.users.table', compact('users'))->render(),
                'info' => $users->total() > 0
                    ? 'Menampilkan <strong class="text-gray-700 dark:text-gray-300">'.$users->firstItem().'–'.$users->lastItem().'</strong> dari <strong class="text-gray-700 dark:text-gray-300">'.$users->total().'</strong> pengguna'
                    : '',
            ]);
        }

        return view('super_admin.users.index', compact(
            'users',
            'totalUsers',
            'activeStaff',
            'totalInstansi'
        ));
    }

    /**
     * Simpan pengguna baru.
     *
     * Gate: create (UserPolicy)
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'size:16', 'unique:users,nik'],
            'email' => ['required', 'email', 'unique:users,email'],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'role' => ['required', Rule::in(UserRole::values())],
            'instansi' => ['nullable', 'string', 'max:100', Rule::requiredIf($request->role === UserRole::AdminGerai->value)],
            'nomor_loket' => ['nullable', 'string', 'max:10'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ]);

        $validated['is_active'] = true;
        $user = User::create($validated);

        AuditLogger::userCreated($user);

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Perbarui data pengguna.
     *
     * Gate: update (UserPolicy)
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        // Snapshot data sebelum perubahan untuk audit trail
        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->value,
            'instansi' => $user->instansi,
            'nomor_loket' => $user->nomor_loket,
            'no_telp' => $user->no_telp,
            'nik' => $user->nik,
        ];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'size:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'role' => ['required', Rule::in(UserRole::values())],
            'instansi' => ['nullable', 'string', 'max:100', Rule::requiredIf($request->role === UserRole::AdminGerai->value)],
            'nomor_loket' => ['nullable', 'string', 'max:10'],
        ]);

        $user->update($validated);

        $after = [
            'name' => $user->fresh()->name,
            'email' => $user->fresh()->email,
            'role' => $user->fresh()->role?->value,
            'instansi' => $user->fresh()->instansi,
            'nomor_loket' => $user->fresh()->nomor_loket,
            'no_telp' => $user->fresh()->no_telp,
            'nik' => $user->fresh()->nik,
        ];

        AuditLogger::userUpdated($user, $before, $after);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif pengguna.
     *
     * Gate: toggleStatus (UserPolicy) — cegah nonaktifkan diri sendiri.
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('toggleStatus', $user);

        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::statusToggled($user, (bool) $user->fresh()->is_active);

        $statusLabel = $user->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$statusLabel}.");
    }

    /**
     * Reset password pengguna dengan password sementara yang aman.
     *
     * Gate: resetPassword (UserPolicy)
     */
    public function resetPassword(User $user)
    {
        $this->authorize('resetPassword', $user);

        // Generate password sementara dengan entropi cukup (12 char)
        $tempPassword = Str::password(12, letters: true, numbers: true, symbols: false);

        $user->update(['password' => Hash::make($tempPassword)]);

        AuditLogger::passwordReset($user);

        return back()->with('temp_password', [
            'user' => $user->name,
            'password' => $tempPassword,
        ]);
    }

    /**
     * Hapus pengguna dari sistem.
     *
     * Gate: delete (UserPolicy) — cegah hapus diri sendiri & sesama SuperAdmin.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Cek apakah user memiliki Booking Aktif (Pending, Checked-In)
        $hasActiveBooking = Booking::where('user_id', $user->id)
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->exists();

        // Cek apakah user memiliki Antrean Aktif (Waiting, Serving)
        $hasActiveQueue = Queue::whereHas('booking', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereIn('status', ['Waiting', 'Serving'])
            ->exists();

        if ($hasActiveBooking || $hasActiveQueue) {
            return redirect()->route('users.index')
                ->with('error', 'Gagal! Akun sedang aktif di antrean atau memiliki booking aktif.');
        }

        // Log sebelum dihapus agar snapshot tetap tersedia
        AuditLogger::userDeleted($user);

        $name = $user->name;
        $user->delete('*');

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Tampilkan log aktivitas untuk seorang pengguna.
     *
     * Gate: viewActivityLog (UserPolicy)
     */
    public function activityLog(User $user)
    {
        $this->authorize('viewActivityLog', $user);

        $logs = ActivityLog::query()
            ->where(function ($q) use ($user) {
                // Log OLEH user ini (sebagai pelaku)
                $q->where('causer_id', $user->id)
                    // ATAU log PADA user ini (sebagai subjek)
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('subject_type', User::class)
                            ->where('subject_id', $user->id);
                    });
            })
            ->with('causer')
            ->latest('created_at')
            ->paginate(20);

        return view('super_admin.users.activity_log', compact('user', 'logs'));
    }
}
