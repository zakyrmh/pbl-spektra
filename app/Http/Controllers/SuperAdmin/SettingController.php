<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\SuperAdmin\SystemSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SettingController extends Controller
{
    public function __construct(
        protected SystemSettingService $settingService
    ) {}

    /**
     * Tampilkan halaman Pengaturan Sistem.
     */
    public function index(): View
    {
        $this->authorize('manage', Setting::class);

        // Ambil semua pengaturan
        $settings = Setting::all()->pluck('value', 'key')->all();

        return view('super_admin.settings', compact('settings'));
    }

    /**
     * Perbarui nilai pengaturan sistem.
     */
    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->authorize('manage', Setting::class);

        $this->settingService->updateSettings($request->validated());

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
