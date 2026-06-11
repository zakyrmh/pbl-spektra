<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Simpan Layanan baru.
     * POST /konfigurasi-gerai-loket/services
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $service = Service::create($validated);

        AuditLogger::log(
            event: 'service_created',
            description: "Layanan baru '{$service->name}' untuk gerai '{$service->department->name}' berhasil ditambahkan.",
            subject: $service,
            properties: ['after' => $service->toArray()]
        );

        return redirect()->route('config.index', ['tab' => 'layanan'])
            ->with('success', "Layanan {$service->name} berhasil ditambahkan.");
    }

    /**
     * Perbarui data Layanan.
     * PUT /konfigurasi-gerai-loket/services/{service}
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $before = $service->toArray();
        $service->fill($validated)->save();

        AuditLogger::log(
            event: 'service_updated',
            description: "Data Layanan '{$service->name}' berhasil diperbarui.",
            subject: $service,
            properties: [
                'before' => $before,
                'after' => Service::find($service->id)->toArray(),
            ]
        );

        return redirect()->route('config.index', ['tab' => 'layanan'])
            ->with('success', "Layanan {$service->name} berhasil diperbarui.");
    }

    /**
     * Hapus Layanan.
     * DELETE /konfigurasi-gerai-loket/services/{service}
     */
    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $name = $service->name;

        AuditLogger::log(
            event: 'service_deleted',
            description: "Layanan '{$name}' berhasil dihapus dari sistem.",
            subject: $service,
            properties: ['snapshot' => $service->toArray()]
        );

        $service->delete();

        return redirect()->route('config.index', ['tab' => 'layanan'])
            ->with('success', "Layanan {$name} berhasil dihapus.");
    }
}
