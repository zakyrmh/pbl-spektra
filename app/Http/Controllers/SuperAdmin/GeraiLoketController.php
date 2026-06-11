<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Data\BoothData;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreBoothRequest;
use App\Http\Requests\SuperAdmin\UpdateBoothRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\BoothManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * GeraiLoketController — Halaman Index & CRUD Konfigurasi Gerai/Loket.
 *
 * Controller ini didelegasikan untuk menampilkan halaman index dengan data aggregate
 * serta menangani operasi CRUD untuk Booth/Gerai (Department).
 */
class GeraiLoketController extends Controller
{
    protected BoothManagementService $boothService;

    public function __construct(BoothManagementService $boothService)
    {
        $this->boothService = $boothService;
    }

    /**
     * Tampilkan halaman dashboard Konfigurasi Gerai / Loket.
     * GET /konfigurasi-gerai-loket
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $data = $this->boothService->getBoothConfigIndexData();

        return view('super_admin.gerai.index', [
            'totalDepartments' => $data['totalDepartments'],
            'totalStaff' => $data['totalStaff'],
            'departments' => $data['departments'],
            'officers' => $data['officers'],
        ]);
    }

    /**
     * Simpan Gerai (Department) baru.
     * POST /konfigurasi-gerai-loket/departments
     */
    public function store(StoreBoothRequest $request): RedirectResponse
    {
        $dto = BoothData::fromRequest($request);
        $department = $this->boothService->createBooth($dto);

        return redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$department->name} berhasil dibuat.");
    }

    /**
     * Perbarui data Gerai.
     * PUT /konfigurasi-gerai-loket/departments/{department}
     */
    public function update(UpdateBoothRequest $request, Department $department): RedirectResponse
    {
        $dto = BoothData::fromRequest($request);
        $this->boothService->updateBooth($department, $dto);

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
        $this->boothService->deleteBooth($department);

        return redirect()->route('config.index', ['tab' => 'gerai'])
            ->with('success', "Gerai {$name} berhasil dihapus.");
    }
}
