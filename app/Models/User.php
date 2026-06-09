<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Notifications\CustomVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'nik', 'email', 'phone_number', 'no_telp', 'avatar_path', 'ktp_photo_path', 'password', 'role', 'departments_id', 'nomor_loket', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
    public function scopeByInstansi($query, $departments_id): void
    {
        $query->where('departments_id', $departments_id);
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
        return $this->department ? $this->department->name : '-';
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
     * Kirim notifikasi verifikasi email dengan kustomisasi branding.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmail);
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
     * Get the notifications for the user.
     *
     * @return HasMany<Notification>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the feedbacks submitted by the user.
     *
     * @return HasMany<Feedback>
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Accessor untuk keselarasan dengan attribute lama phone_number.
     */
    public function getPhoneNumberAttribute(): ?string
    {
        return $this->no_telp;
    }

    /**
     * Mutator untuk keselarasan dengan attribute lama phone_number.
     */
    public function setPhoneNumberAttribute(?string $value): void
    {
        $this->attributes['no_telp'] = $value;
    }

    /**
     * Get the department that owns this user.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departments_id');
    }

    /**
     * Sesi loket fisik petugas secara dinamis dari departemennya.
     */
    public function getCounterAttribute(): ?Counter
    {
        if (! $this->departments_id) {
            return null;
        }

        return Counter::where('department_id', $this->departments_id)->first();
    }

    /**
     * Dapatkan ID loket secara dinamis.
     */
    public function getCounterIdAttribute(): ?int
    {
        return $this->counter?->id;
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
