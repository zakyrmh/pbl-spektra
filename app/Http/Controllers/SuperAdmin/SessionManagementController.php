<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SuperAdmin\SessionManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller untuk manajemen sesi aktif pengguna.
 *
 * Super Admin dapat melihat semua sesi aktif milik user tertentu
 * dan melakukan force-revoke (paksa logout) satu atau semua sesi.
 */
final class SessionManagementController extends Controller
{
    public function __construct(
        protected SessionManagementService $sessionService
    ) {}

    /**
     * Tampilkan daftar sesi aktif untuk seorang pengguna.
     *
     * Gate: manageSessions (UserPolicy)
     */
    public function index(User $user): View
    {
        $this->authorize('manageSessions', $user);

        $sessions = $this->sessionService->getUserSessions($user);

        return view('super_admin.users.sessions', compact('user', 'sessions'));
    }

    /**
     * Hapus (revoke) satu sesi tertentu milik user.
     *
     * Gate: manageSessions (UserPolicy)
     */
    public function destroy(User $user, string $sessionId): RedirectResponse
    {
        $this->authorize('manageSessions', $user);

        $this->sessionService->revokeSession($user, $sessionId);

        return redirect()->route('users.sessions.index', $user)
            ->with('success', 'Sesi berhasil dihentikan paksa.');
    }

    /**
     * Hapus (revoke) SEMUA sesi aktif milik user.
     *
     * Gate: manageSessions (UserPolicy)
     */
    public function destroyAll(User $user): RedirectResponse
    {
        $this->authorize('manageSessions', $user);

        $this->sessionService->revokeAllSessions($user);

        return redirect()->route('users.index')
            ->with('success', "Semua sesi aktif {$user->name} berhasil dihentikan paksa.");
    }
}
