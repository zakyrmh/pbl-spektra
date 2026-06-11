<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan Sistem.
     */
    public function index(): View
    {
        // Hanya Super Admin yang boleh masuk
        if (Auth::user()->role !== UserRole::SuperAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        // Ambil semua pengaturan
        $settings = Setting::all()->pluck('value', 'key')->all();

        return view('super_admin.settings', compact('settings'));
    }

    /**
     * Perbarui nilai pengaturan sistem.
     */
    public function update(Request $request): RedirectResponse
    {
        // Hanya Super Admin yang boleh melakukan aksi ini
        if (Auth::user()->role !== UserRole::SuperAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'app_logo' => ['required', 'string', 'max:255'],
            'maintenance_mode' => ['required', 'in:0,1'],
            'marquee_text' => ['required', 'string', 'max:500'],
            'marquee_active' => ['required', 'in:0,1'],
            'reverb_host' => ['required', 'string', 'max:255'],
            'reverb_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'reverb_scheme' => ['required', 'in:http,https'],
            'websocket_enabled' => ['required', 'in:0,1'],
        ]);

        $before = [];
        $after = [];

        foreach ($validated as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            $oldVal = $setting ? $setting->value : null;

            if ($oldVal !== $value) {
                $before[$key] = $oldVal;
                $after[$key] = $value;

                Setting::setVal($key, $value === null ? null : (string) $value);
            }
        }

        if (! empty($after)) {
            // Catat log aktivitas audit trail
            AuditLogger::log(
                event: 'settings_updated',
                description: 'Super Admin memperbarui konfigurasi pengaturan sistem.',
                properties: [
                    'before' => $before,
                    'after' => $after,
                ]
            );
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
