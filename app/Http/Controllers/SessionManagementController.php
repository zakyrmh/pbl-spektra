<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk manajemen sesi aktif pengguna.
 *
 * Super Admin dapat melihat semua sesi aktif milik user tertentu
 * dan melakukan force-revoke (paksa logout) satu atau semua sesi.
 *
 * Sumber data: tabel `sessions` bawaan Laravel (DatabaseSessionDriver).
 */
class SessionManagementController extends Controller
{
    /**
     * Tampilkan daftar sesi aktif untuk seorang pengguna.
     *
     * Gate: manageSessions (UserPolicy)
     */
    public function index(User $user)
    {
        $this->authorize('manageSessions', $user);

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) {
                $session->last_activity_at = Carbon::createFromTimestamp($session->last_activity);
                $session->is_recent = $session->last_activity_at->diffInMinutes(now()) <= 15;

                // Parse user agent menjadi info browser & OS yang mudah dibaca
                $session->browser_info = self::parseBrowserInfo($session->user_agent ?? '');

                return $session;
            });

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

        $deleted = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();

        if ($deleted) {
            AuditLogger::sessionRevoked($user, $sessionId);
        }

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

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        AuditLogger::sessionRevoked($user, 'all');

        return redirect()->route('users.index')
            ->with('success', "Semua sesi aktif {$user->name} berhasil dihentikan paksa.");
    }

    /**
     * Parse user agent string menjadi representasi singkat yang mudah dibaca.
     *
     * @return array{browser: string, os: string, device: string}
     */
    private static function parseBrowserInfo(string $userAgent): array
    {
        $browser = 'Browser tidak diketahui';
        $os = 'OS tidak diketahui';
        $device = 'Desktop';

        // Deteksi browser
        if (str_contains($userAgent, 'Edg/')) {
            $browser = 'Microsoft Edge';
        } elseif (str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera')) {
            $browser = 'Opera';
        } elseif (str_contains($userAgent, 'Chrome/')) {
            $browser = 'Google Chrome';
        } elseif (str_contains($userAgent, 'Firefox/')) {
            $browser = 'Mozilla Firefox';
        } elseif (str_contains($userAgent, 'Safari/')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'curl/')) {
            $browser = 'cURL (API/Bot)';
        }

        // Deteksi OS
        if (str_contains($userAgent, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $os = 'iOS';
        } elseif (str_contains($userAgent, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) {
            $os = 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        }

        // Deteksi device
        if (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
            $device = 'Tablet';
        } elseif (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android')) {
            $device = 'Mobile';
        }

        return compact('browser', 'os', 'device');
    }
}
