<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Models\Setting;
use App\Services\AuditLogger;

class SystemSettingService
{
    /**
     * Perbarui nilai pengaturan sistem dan catat audit log jika ada perubahan.
     *
     * @param  array<string, mixed>  $settingsData
     */
    public function updateSettings(array $settingsData): void
    {
        $before = [];
        $after = [];

        foreach ($settingsData as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            $oldVal = $setting ? $setting->value : null;

            if ($oldVal !== $value) {
                $before[$key] = $oldVal;
                $after[$key] = $value;

                Setting::setVal($key, $value === null ? null : (string) $value);
            }
        }

        if (! empty($after)) {
            AuditLogger::log(
                event: 'settings_updated',
                description: 'Super Admin memperbarui konfigurasi pengaturan sistem.',
                properties: [
                    'before' => $before,
                    'after' => $after,
                ]
            );
        }
    }
}
