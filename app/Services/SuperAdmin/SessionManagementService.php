<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Data\SuperAdmin\UserSessionData;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SessionManagementService
{
    /**
     * Dapatkan daftar sesi aktif pengguna dalam bentuk koleksi UserSessionData.
     *
     * @return Collection<int, UserSessionData>
     */
    public function getUserSessions(User $user): Collection
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function (object $session) {
                $lastActivityAt = Carbon::createFromTimestamp($session->last_activity);
                $isRecent = $lastActivityAt->diffInMinutes(now()) <= 15;
                $browserInfo = $this->parseBrowserInfo($session->user_agent ?? '');

                return UserSessionData::fromRow(
                    row: $session,
                    browserInfo: $browserInfo,
                    lastActivityAt: $lastActivityAt,
                    isRecent: $isRecent
                );
            });
    }

    /**
     * Hapus satu sesi tertentu.
     */
    public function revokeSession(User $user, string $sessionId): bool
    {
        $deleted = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();

        if ($deleted > 0) {
            AuditLogger::sessionRevoked($user, $sessionId);

            return true;
        }

        return false;
    }

    /**
     * Hapus semua sesi untuk user.
     */
    public function revokeAllSessions(User $user): void
    {
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        AuditLogger::sessionRevoked($user, 'all');
    }

    /**
     * Parse user agent string menjadi representasi singkat yang mudah dibaca.
     *
     * @return array{browser: string, os: string, device: string}
     */
    public function parseBrowserInfo(string $userAgent): array
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
