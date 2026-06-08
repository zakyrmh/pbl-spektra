<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * Service AuditLogger — mencatat semua aktivitas penting ke tabel activity_logs.
 *
 * Cara penggunaan di Controller:
 *
 *   AuditLogger::log(
 *       event: 'created',
 *       description: "Pengguna {$user->name} berhasil dibuat.",
 *       subject: $user,
 *       properties: ['after' => $user->toArray()],
 *   );
 *
 * Atau untuk event tanpa subjek model (seperti login):
 *
 *   AuditLogger::log(event: 'login', description: 'User login berhasil.');
 */
class AuditLogger
{
    /**
     * Catat satu entri aktivitas.
     *
     * @param  string  $event  Nama event singkat (created, updated, deleted, login, dll.)
     * @param  string  $description  Deskripsi human-readable dalam Bahasa Indonesia
     * @param  Model|null  $subject  Objek yang dikenai aksi (polymorphic)
     * @param  array<mixed>  $properties  Data before/after atau metadata tambahan
     * @param  User|null  $causer  Pelaku aksi; default ke user yang sedang login
     * @param  Request|null  $request  Request HTTP; default ke request saat ini
     */
    public static function log(
        string $event,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?User $causer = null,
        ?Request $request = null,
    ): ActivityLog {
        /** @var User|null $actor */
        $actor = $causer ?? Auth::user();
        $request = $request ?? RequestFacade::instance();

        return ActivityLog::create([
            'causer_id' => $actor?->id,
            'subject_id' => $subject?->getKey(),
            'subject_type' => $subject ? $subject::class : null,
            'event' => $event,
            'description' => $description,
            'properties' => empty($properties) ? null : $properties,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    // ──────────────────────────────────────────────────
    // Shorthand methods untuk event umum User Management
    // ──────────────────────────────────────────────────

    /**
     * Log saat pengguna baru dibuat.
     */
    public static function userCreated(User $newUser): ActivityLog
    {
        return self::log(
            event: 'user_created',
            description: "Pengguna baru '{$newUser->name}' ({$newUser->email}) berhasil dibuat dengan peran {$newUser->role->label()}.",
            subject: $newUser,
            properties: [
                'after' => [
                    'name' => $newUser->name,
                    'email' => $newUser->email,
                    'role' => $newUser->role->value,
                    'departments_id' => $newUser->departments_id,
                    'nomor_loket' => $newUser->nomor_loket,
                ],
            ],
        );
    }

    /**
     * Log saat data pengguna diperbarui.
     *
     * @param  array<string, mixed>  $before  Data sebelum update
     * @param  array<string, mixed>  $after  Data sesudah update
     */
    public static function userUpdated(User $user, array $before, array $after): ActivityLog
    {
        return self::log(
            event: 'user_updated',
            description: "Data pengguna '{$user->name}' ({$user->email}) berhasil diperbarui.",
            subject: $user,
            properties: compact('before', 'after'),
        );
    }

    /**
     * Log saat status akun di-toggle.
     */
    public static function statusToggled(User $user, bool $isNowActive): ActivityLog
    {
        $status = $isNowActive ? 'diaktifkan' : 'dinonaktifkan';

        return self::log(
            event: 'status_toggled',
            description: "Akun '{$user->name}' ({$user->email}) berhasil {$status}.",
            subject: $user,
            properties: ['is_active' => $isNowActive],
        );
    }

    /**
     * Log saat password pengguna direset oleh admin.
     */
    public static function passwordReset(User $user): ActivityLog
    {
        return self::log(
            event: 'password_reset',
            description: "Password akun '{$user->name}' ({$user->email}) berhasil direset oleh admin.",
            subject: $user,
        );
    }

    /**
     * Log saat pengguna dihapus.
     */
    public static function userDeleted(User $user): ActivityLog
    {
        return self::log(
            event: 'user_deleted',
            description: "Pengguna '{$user->name}' ({$user->email}) berhasil dihapus dari sistem.",
            subject: $user,
            properties: [
                'snapshot' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'departments_id' => $user->departments_id,
                ],
            ],
        );
    }

    /**
     * Log saat sesi user di-revoke oleh admin.
     */
    public static function sessionRevoked(User $targetUser, string $sessionId = 'all'): ActivityLog
    {
        $desc = $sessionId === 'all'
            ? "Semua sesi aktif '{$targetUser->name}' ({$targetUser->email}) berhasil dihentikan paksa."
            : "Satu sesi aktif '{$targetUser->name}' ({$targetUser->email}) berhasil dihentikan paksa.";

        return self::log(
            event: 'session_revoked',
            description: $desc,
            subject: $targetUser,
            properties: ['session_id' => $sessionId],
        );
    }

    /**
     * Log saat user berhasil login.
     */
    public static function userLoggedIn(User $user): ActivityLog
    {
        return self::log(
            event: 'login',
            description: "Pengguna '{$user->name}' berhasil masuk ke sistem.",
            subject: $user,
            causer: $user,
        );
    }

    /**
     * Log saat user logout.
     */
    public static function userLoggedOut(User $user): ActivityLog
    {
        return self::log(
            event: 'logout',
            description: "Pengguna '{$user->name}' berhasil keluar dari sistem.",
            subject: $user,
            causer: $user,
        );
    }
}
