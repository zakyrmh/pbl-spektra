<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CounterConfigController extends Controller
{
    /**
     * Simpan Loket baru.
     * POST /konfigurasi-gerai-loket/counters
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ]);

        $counter = Counter::create([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'status' => $validated['status'],
        ]);

        // Sinkronisasi layanan
        if (! empty($validated['services'])) {
            $counter->services()->sync($validated['services']);
        }

        // Penugasan petugas
        if ($request->filled('officer_id')) {
            User::where('id', $validated['officer_id'])->update(['departments_id' => $counter->department_id]);
        }

        AuditLogger::log(
            event: 'counter_created',
            description: "Loket baru '{$counter->name}' untuk gerai '{$counter->department->name}' berhasil dibuat.",
            subject: $counter,
            properties: [
                'after' => $counter->toArray(),
                'assigned_officer_id' => $request->officer_id,
            ]
        );

        return redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$counter->name} berhasil dibuat.");
    }

    /**
     * Perbarui data Loket.
     * PUT /konfigurasi-gerai-loket/counters/{counter}
     */
    public function update(Request $request, Counter $counter): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ]);

        $before = $counter->toArray();

        $counter->fill([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'status' => $validated['status'],
        ])->save();

        $counter->services()->sync($validated['services'] ?? []);

        // Reset petugas lama, lalu assign petugas baru
        User::where('departments_id', $counter->department_id)->update(['departments_id' => null]);

        if ($request->filled('officer_id')) {
            User::where('id', $validated['officer_id'])->update(['departments_id' => $counter->department_id]);
        }

        AuditLogger::log(
            event: 'counter_updated',
            description: "Data Loket '{$counter->name}' berhasil diperbarui.",
            subject: $counter,
            properties: [
                'before' => $before,
                'after' => Counter::find($counter->id)->toArray(),
                'assigned_officer_id' => $request->officer_id,
                'assigned_services' => $request->services ?? [],
            ]
        );

        return redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$counter->name} berhasil diperbarui.");
    }

    /**
     * Hapus Loket.
     * DELETE /konfigurasi-gerai-loket/counters/{counter}
     */
    public function destroy(Counter $counter): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $name = $counter->name;

        // Reset petugas yang aktif di loket ini
        User::where('departments_id', $counter->department_id)->update(['departments_id' => null]);

        AuditLogger::log(
            event: 'counter_deleted',
            description: "Loket '{$name}' berhasil dihapus dari sistem.",
            subject: $counter,
            properties: ['snapshot' => $counter->toArray()]
        );

        $counter->delete();

        return redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$name} berhasil dihapus.");
    }

    /**
     * Toggle status loket.
     * PATCH /konfigurasi-gerai-loket/counters/{counter}/status
     */
    public function toggleStatus(Request $request, Counter $counter): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
        ]);

        $oldStatus = $counter->status;
        $counter->status = $validated['status'];
        $counter->save();

        AuditLogger::log(
            event: 'counter_status_toggled',
            description: "Status loket '{$counter->name}' diubah dari '{$oldStatus}' menjadi '{$counter->status}'.",
            subject: $counter,
            properties: [
                'old_status' => $oldStatus,
                'new_status' => $counter->status,
            ]
        );

        return back()->with('success', "Status loket {$counter->name} berhasil diubah menjadi: {$counter->status}.");
    }
}
