<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class DepartmentController extends Controller
{
    /**
     * Simpan Gerai (Department) baru.
     * POST /konfigurasi-gerai-loket/departments
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['logo'] = $this->processLogoUpload($request);

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

    /**
     * Perbarui data Gerai.
     * PUT /konfigurasi-gerai-loket/departments/{department}
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'inisial' => ['required', 'string', 'max:6', 'unique:departments,inisial,'.$department->id],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $before = $department->toArray();

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($department->logo) {
                app('filesystem')->disk('public')->delete($department->logo);
            }
            $validated['logo'] = $this->processLogoUpload($request);
        }

        $department->fill($validated)->save();

        AuditLogger::log(
            event: 'department_updated',
            description: "Data Gerai '{$department->name}' berhasil diperbarui.",
            subject: $department,
            properties: [
                'before' => $before,
                'after' => Department::find($department->id)->toArray(),
            ]
        );

        return redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$department->name} berhasil diperbarui.");
    }

    /**
     * Hapus Gerai.
     * DELETE /konfigurasi-gerai-loket/departments/{department}
     */
    public function destroy(Department $department): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $name = $department->name;

        if ($department->logo) {
            app('filesystem')->disk('public')->delete($department->logo);
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

    /**
     * Proses upload logo dan konversi ke WebP.
     */
    private function processLogoUpload(Request $request): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }

        $file = $request->file('logo');
        $filename = 'logos/'.bin2hex(random_bytes(20)).'.webp';
        $manager = new ImageManager(new Driver);
        $encoded = $manager->decode($file->getContent())->encode(new WebpEncoder(quality: 80));

        app('filesystem')->disk('public')->put($filename, $encoded->toString());

        return $filename;
    }
}
