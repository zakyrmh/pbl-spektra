<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'nik', 'email', 'phone_number', 'avatar_path', 'ktp_photo_path', 'password', 'role', 'instansi', 'nomor_loket', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Daftar instansi/gerai yang tersedia di MPP Sawahlunto.
     *
     * @var array<string, string>
     */
    public static array $instansiList = [
        'Disdukcapil' => 'Dinas Kependudukan & Pencatatan Sipil',
        'Imigrasi' => 'Kantor Imigrasi',
        'Samsat' => 'Samsat / Bapenda',
        'DPMPTSP' => 'Dinas Penanaman Modal & PTSP',
        'BPJS_Kesehatan' => 'BPJS Kesehatan',
        'BPJS_Ketenagakerjaan' => 'BPJS Ketenagakerjaan',
        'BPN' => 'Badan Pertanahan Nasional',
        'Disnaker' => 'Dinas Ketenagakerjaan',
        'Dinas_Pendidikan' => 'Dinas Pendidikan',
        'Dinas_Kesehatan' => 'Dinas Kesehatan',
        'PLN' => 'PLN',
        'PDAM' => 'PDAM',
        'Front_Office' => 'Front Office MPP',
    ];

    /**
     * Cast kolom model ke tipe yang tepat.
     *
     * Kolom `role` di-cast ke `UserRole` Enum secara native.
     * Laravel menyimpan/membaca nilai string dari DB dan mengonversinya otomatis.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    // ──────────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────────

    /**
     * Filter berdasarkan role tertentu.
     *
     * @param  Builder  $query
     */
    public function scopeByRole($query, UserRole|string $role): void
    {
        $value = $role instanceof UserRole ? $role->value : $role;
        $query->where('role', $value);
    }

    /**
     * Filter berdasarkan instansi tertentu.
     *
     * @param  Builder  $query
     */
    public function scopeByInstansi($query, string $instansi): void
    {
        $query->where('instansi', $instansi);
    }

    /** Filter hanya akun aktif. */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /** Filter pengguna yang sedang online (login dalam 15 menit terakhir). */
    public function scopeOnline($query): void
    {
        $query->where('last_login_at', '>=', now()->subMinutes(15));
    }

    // ──────────────────────────────────────────────────
    // Accessors & Helpers
    // ──────────────────────────────────────────────────

    /**
     * Label role human-readable Bahasa Indonesia.
     * Delegasi ke UserRole Enum agar single source of truth.
     */
    public function getRoleLabelAttribute(): string
    {
        return $this->role instanceof UserRole
            ? $this->role->label()
            : ucfirst($this->role ?? '');
    }

    /**
     * Badge CSS class dari UserRole Enum.
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return $this->role instanceof UserRole
            ? $this->role->badgeClass()
            : 'bg-gray-100 text-gray-600';
    }

    /**
     * Label instansi lengkap dari daftar.
     */
    public function getInstansiLabelAttribute(): string
    {
        return self::$instansiList[$this->instansi] ?? ($this->instansi ?? '-');
    }

    /**
     * Apakah user sedang online (login ≤15 menit lalu).
     */
    public function isOnline(): bool
    {
        return $this->last_login_at !== null
            && $this->last_login_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Get the public URL of the user's avatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=1B4FA8&background=EFF2F7';
    }

    /**
     * Get the public URL of the user's KTP photo.
     */
    public function getKtpPhotoUrlAttribute(): ?string
    {
        return $this->ktp_photo_path
            ? asset('storage/'.$this->ktp_photo_path)
            : null;
    }

    // ──────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────

    /**
     * Log aktivitas yang dilakukan oleh user ini (sebagai pelaku).
     *
     * @return HasMany<ActivityLog>
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'causer_id');
    }

    /**
     * Log aktivitas yang terjadi PADA user ini (sebagai subjek).
     *
     * @return HasMany<ActivityLog>
     */
    public function subjectLogs()
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', self::class);
    }

    /**
     * Sesi loket fisik petugas.
     */
    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    /**
     * Bookings made by this user (customer).
     *
     * @return HasMany<Booking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
