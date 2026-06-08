<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * Matikan timestamp created_at karena hanya ada updated_at di tabel settings.
     */
    public const CREATED_AT = null;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'description',
        'updated_by',
    ];

    /**
     * User yang terakhir kali memperbarui pengaturan ini.
     *
     * @return BelongsTo<User, self>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Ambil nilai pengaturan berdasarkan key.
     * Menggunakan Laravel Cache (rememberForever) untuk optimalisasi performa.
     */
    public static function getVal(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Simpan atau perbarui nilai pengaturan, lalu bersihkan cache terkait.
     */
    public static function setVal(string $key, ?string $value, ?string $description = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'updated_by' => auth()->id(),
            ]
        );

        // Hapus cache agar nilai baru langsung terbaca
        Cache::forget("setting.{$key}");

        return $setting;
    }
}
