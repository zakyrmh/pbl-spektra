<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class GeraiLoketController extends Controller
{
    /**
     * Tampilkan halaman dashboard Konfigurasi Gerai / Loket.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class); // Hanya Super Admin (sesuai UserPolicy)

        // ── Metrics ──────────────────────────────────────────────
        $totalDepartments = Department::query()->count('*');
        $totalStaff = User::query()
            ->where('role', UserRole::AdminGerai->value)
            ->count('*');

        // ── Data List ────────────────────────────────────────────
        $departments = Department::query()->withCount(['counters', 'services'])->latest()->get();

        $counters = Counter::query()->with(['department', 'users', 'services'])->latest()->get();

        $services = Service::query()->with('department')->latest()->get();

        // List petugas loket (role: admin_gerai) untuk form penugasan
        $officers = User::query()->where('role', '=', UserRole::AdminGerai->value, 'and')->get();

        return view('super_admin.gerai.index', compact(
            'totalDepartments',
            'totalStaff',
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

        $validated = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial'],
            'logo' => ['nullable', 'image', 'max:2048'], // Maks 2MB
            'description' => ['nullable', 'string'],
        ])->validate();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logos/'.bin2hex(random_bytes(20)).'.webp';

            // Kompresi dan ubah ke webp (quality 80)
            $manager = new ImageManager(new Driver);
            $encoded = $manager->decode($file->getContent())->encode(new WebpEncoder(quality: 80));

            // Simpan ke disk public
            app('filesystem')->disk('public')->put($filename, $encoded->toString());

            $validated['logo'] = $filename;
        }

        $department = Department::query()->create($validated);

        AuditLogger::log(
            event: 'department_created',
            description: "Gerai baru '{$department->name}' (Inisial: {$department->inisial}) berhasil dibuat.",
            subject: $department,
            properties: ['after' => $department->toArray()]
        );

        return \redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$department->name} berhasil dibuat.");
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $this->authorize('viewAny', User::class);

        $validated = validator($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial,'.$department->id],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ])->validate();

        $before = $department->toArray();

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($department->logo) {
                app('filesystem')->disk('public')->delete($department->logo);
            }

            $file = $request->file('logo');
            $filename = 'logos/'.bin2hex(random_bytes(20)).'.webp';

            // Kompresi dan ubah ke webp (quality 80)
            $manager = new ImageManager(new Driver);
            $encoded = $manager->decode($file->getContent())->encode(new WebpEncoder(quality: 80));

            // Simpan ke disk public
            app('filesystem')->disk('public')->put($filename, $encoded->toString());

            $validated['logo'] = $filename;
        }

        $department->fill($validated);
        $department->save();

        AuditLogger::log(
            event: 'department_updated',
            description: "Data Gerai '{$department->name}' berhasil diperbarui.",
            subject: $department,
            properties: [
                'before' => $before,
                'after' => Department::query()->find($department->id)->toArray(),
            ]
        );

        return \redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$department->name} berhasil diperbarui.");
    }

    public function destroyDepartment(Department $department)
    {
        $this->authorize('viewAny', User::class);

        $name = $department->name;

        // Hapus file logo dari storage
        if ($department->logo) {
            app('filesystem')->disk('public')->delete($department->logo);
        }

        AuditLogger::log(
            event: 'department_deleted',
            description: "Gerai '{$name}' (Inisial: {$department->inisial}) dihapus dari sistem.",
            subject: $department,
            properties: ['snapshot' => $department->toArray()]
        );

        $department->{'delete'}();

        return \redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$name} berhasil dihapus.");
    }

    // ─────────────────────────────────────────────────────────
    // CRUD LOKET (COUNTER)
    // ─────────────────────────────────────────────────────────

    public function storeCounter(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = validator($request->all(), [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ])->validate();

        $counter = Counter::query()->create([
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
            // Lepas petugas terpilih dari loket lamanya (jika ada) dengan menset departments_id baru
            User::query()->where('id', '=', $validated['officer_id'], 'and')->update(['departments_id' => $counter->department_id]);
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

        return \redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$counter->name} berhasil dibuat.");
    }

    public function updateCounter(Request $request, Counter $counter)
    {
        $this->authorize('viewAny', User::class);

        $validated = validator($request->all(), [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'services' => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ])->validate();

        $before = $counter->toArray();

        $counter->fill([
            'department_id' => $validated['department_id'],
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'status' => $validated['status'],
        ]);
        $counter->save();

        // Mapping Services
        $counter->services()->sync($validated['services'] ?? []);

        // Plotting Petugas (Officer Assignment)
        // Reset petugas yang sebelumnya ditugaskan di loket ini
        User::query()->where('departments_id', '=', $counter->department_id, 'and')->update(['departments_id' => null]);

        if ($request->filled('officer_id')) {
            // Plotting petugas terpilih ke loket ini
            User::query()->where('id', '=', $validated['officer_id'], 'and')->update(['departments_id' => $counter->department_id]);
        }

        AuditLogger::log(
            event: 'counter_updated',
            description: "Data Loket '{$counter->name}' berhasil diperbarui.",
            subject: $counter,
            properties: [
                'before' => $before,
                'after' => Counter::query()->find($counter->id)->toArray(),
                'assigned_officer_id' => $request->officer_id,
                'assigned_services' => $request->services ?? [],
            ]
        );

        return \redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$counter->name} berhasil diperbarui.");
    }

    public function destroyCounter(Counter $counter)
    {
        $this->authorize('viewAny', User::class);

        $name = $counter->name;

        // Reset petugas yang sedang aktif di loket ini
        User::query()->where('departments_id', '=', $counter->department_id, 'and')->update(['departments_id' => null]);

        AuditLogger::log(
            event: 'counter_deleted',
            description: "Loket '{$name}' berhasil dihapus dari sistem.",
            subject: $counter,
            properties: ['snapshot' => $counter->toArray()]
        );

        $counter->{'delete'}();

        return \redirect()->route('config.index', ['tab' => 'loket'])
            ->with('success', "Loket {$name} berhasil dihapus.");
    }

    public function toggleCounterStatus(Request $request, Counter $counter)
    {
        $this->authorize('viewAny', User::class);

        $validated = validator($request->all(), [
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
        ])->validate();

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

    // ─────────────────────────────────────────────────────────
    // CRUD LAYANAN (SERVICE)
    // ─────────────────────────────────────────────────────────

    public function storeService(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = validator($request->all(), [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ])->validate();

        $service = Service::query()->create($validated);

        AuditLogger::log(
            event: 'service_created',
            description: "Layanan baru '{$service->name}' untuk gerai '{$service->department->name}' berhasil ditambahkan.",
            subject: $service,
            properties: ['after' => $service->toArray()]
        );

        return \redirect()->route('config.index', ['tab' => 'layanan'])
            ->with('success', "Layanan {$service->name} berhasil ditambahkan.");
    }

    public function updateService(Request $request, Service $service)
    {
        $this->authorize('viewAny', User::class);

        $validated = validator($request->all(), [
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ])->validate();

        $before = $service->toArray();
        $service->fill($validated);
        $service->save();

        AuditLogger::log(
            event: 'service_updated',
            description: "Data Layanan '{$service->name}' berhasil diperbarui.",
            subject: $service,
            properties: [
                'before' => $before,
                'after' => Service::query()->find($service->id)->toArray(),
            ]
        );

        return \redirect()->route('config.index', ['tab' => 'layanan'])
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

        $service->{'delete'}();

        return \redirect()->route('config.index', ['tab' => 'layanan'])
            ->with('success', "Layanan {$name} berhasil dihapus.");
    }
}
