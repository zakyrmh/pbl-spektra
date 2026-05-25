<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GeraiLoketController extends Controller
{
    /**
     * Tampilkan halaman dashboard Konfigurasi Gerai / Loket.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class); // Hanya Super Admin (sesuai UserPolicy)

        // ── Metrics ──────────────────────────────────────────────
        $totalDepartments = Department::count();
        $totalCountersActive = Counter::where('status', 'aktif')->count();
        $totalStaffStandby = User::where('role', UserRole::AdminGerai)
            ->where(function ($query) {
                $query->whereNull('counter_id')
                    ->orWhereNotExists(function ($q) {
                        $q->selectRaw(1)
                            ->from('counters')
                            ->whereColumn('counters.id', 'users.counter_id');
                    });
            })
            ->count();

        // ── Data List ────────────────────────────────────────────
        $departments = Department::withCount(['counters', 'services'])->latest()->get();

        $counters = Counter::with(['department', 'users', 'services'])->latest()->get();

        $services = Service::with('department')->latest()->get();

        // List petugas loket (role: admin_gerai) untuk form penugasan
        $officers = User::where('role', UserRole::AdminGerai)->get();

        return view('super_admin.config.index', compact(
            'totalDepartments',
            'totalCountersActive',
            'totalStaffStandby',
            'departments',
            'counters',
            'services',
            'officers'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // CRUD GERAI (DEPARTMENT)
    // ─────────────────────────────────────────────────────────

    public function storeDepartment(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial'],
            'logo' => ['nullable', 'image', 'max:2048'], // Maks 2MB
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $department = Department::create($validated);

        AuditLogger::log(
            event: 'department_created',
            description: "Gerai baru '{$department->name}' (Inisial: {$department->inisial}) berhasil dibuat.",
            subject: $department,
            properties: ['after' => $department->toArray()]
        );

        return redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$department->name} berhasil dibuat.");
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', Rule::unique('departments', 'inisial')->ignore($department->id)],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $before = $department->toArray();

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($department->logo) {
                Storage::disk('public')->delete($department->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $department->update($validated);

        AuditLogger::log(
            event: 'department_updated',
            description: "Data Gerai '{$department->name}' berhasil diperbarui.",
            subject: $department,
            properties: [
                'before' => $before,
                'after' => $department->fresh()->toArray(),
            ]
        );

        return redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$department->name} berhasil diperbarui.");
    }

    public function destroyDepartment(Department $department)
    {
        $this->authorize('viewAny', User::class);

        $name = $department->name;

        // Hapus file logo dari storage
        if ($department->logo) {
            Storage::disk('public')->delete($department->logo);
        }

        AuditLogger::log(
            event: 'department_deleted',
            description: "Gerai '{$name}' (Inisial: {$department->inisial}) dihapus dari sistem.",
            subject: $department,
            properties: ['snapshot' => $department->toArray()]
        );

        $department->delete();

        return redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$name} berhasil dihapus.");
    }

    // ─────────────────────────────────────────────────────────
    // CRUD LOKET (COUNTER)
    // ─────────────────────────────────────────────────────────

    public function storeCounter(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif', 'istirahat'])],
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

        // Mapping Services
        if (! empty($validated['services'])) {
            $counter->services()->sync($validated['services']);
        }

        // Plotting Petugas (Officer Assignment)
        if ($request->filled('officer_id')) {
            // Lepas petugas terpilih dari loket lamanya (jika ada)
            User::where('id', $validated['officer_id'])->update(['counter_id' => $counter->id]);
        }

        AuditLogger::log(
            event: 'counter_created',
            description: "Loket baru '{$counter->name}' untuk gerai '{$counter->department->name}' berhasil dibuat.",
            subject: $counter,
            properties: [
                'after' => $counter->toArray(),
                'assigned_officer_id' => $request->officer_id,
                'assigned_services' => $request->services ?? [],
            ]
        );

        return redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$counter->name} berhasil dibuat.");
    }

    public function updateCounter(Request $request, Counter $counter)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif', 'istirahat'])],
            'officer_id' => ['nullable', 'exists:users,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ]);

        $before = $counter->toArray();

        $counter->update([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'status' => $validated['status'],
        ]);

        // Mapping Services
        $counter->services()->sync($validated['services'] ?? []);

        // Plotting Petugas (Officer Assignment)
        // Reset petugas yang sebelumnya ditugaskan di loket ini
        User::where('counter_id', $counter->id)->update(['counter_id' => null]);

        if ($request->filled('officer_id')) {
            // Plotting petugas terpilih ke loket ini
            User::where('id', $validated['officer_id'])->update(['counter_id' => $counter->id]);
        }

        AuditLogger::log(
            event: 'counter_updated',
            description: "Data Loket '{$counter->name}' berhasil diperbarui.",
            subject: $counter,
            properties: [
                'before' => $before,
                'after' => $counter->fresh()->toArray(),
                'assigned_officer_id' => $request->officer_id,
                'assigned_services' => $request->services ?? [],
            ]
        );

        return redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$counter->name} berhasil diperbarui.");
    }

    public function destroyCounter(Counter $counter)
    {
        $this->authorize('viewAny', User::class);

        $name = $counter->name;

        // Reset petugas yang sedang aktif di loket ini
        User::where('counter_id', $counter->id)->update(['counter_id' => null]);

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

    public function toggleCounterStatus(Request $request, Counter $counter)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['aktif', 'nonaktif', 'istirahat'])],
        ]);

        $oldStatus = $counter->status;
        $counter->update(['status' => $validated['status']]);

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

    // ─────────────────────────────────────────────────────────
    // CRUD LAYANAN (SERVICE)
    // ─────────────────────────────────────────────────────────

    public function storeService(Request $request)
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

    public function updateService(Request $request, Service $service)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $before = $service->toArray();
        $service->update($validated);

        AuditLogger::log(
            event: 'service_updated',
            description: "Data Layanan '{$service->name}' berhasil diperbarui.",
            subject: $service,
            properties: [
                'before' => $before,
                'after' => $service->fresh()->toArray(),
            ]
        );

        return redirect()->route('config.index', ['tab' => 'layanan'])
            ->with('success', "Layanan {$service->name} berhasil diperbarui.");
    }

    public function destroyService(Service $service)
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
