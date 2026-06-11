<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * GeraiLoketController — Halaman Index Konfigurasi Gerai/Loket.
 *
 * Controller ini hanya bertanggung jawab menampilkan halaman index
 * dengan data aggregate. Operasi CRUD didelegasikan ke:
 *   - SuperAdmin\DepartmentController    (CRUD Gerai)
 *   - SuperAdmin\CounterConfigController (CRUD Loket)
 *   - SuperAdmin\ServiceController       (CRUD Layanan)
 */
class GeraiLoketController extends Controller
{
    /**
     * Tampilkan halaman dashboard Konfigurasi Gerai / Loket.
     * GET /konfigurasi-gerai-loket
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        // Metrics
        $totalDepartments = Department::query()->count('*');
        $totalStaff = User::query()->where('role', UserRole::AdminGerai->value)->count('*');

        // Data List
        $departments = Department::query()->withCount(['counters', 'services'])->latest()->get();
        $counters = Counter::query()->with(['department', 'users', 'services'])->latest()->get();
        $services = Service::query()->with('department')->latest()->get();

        // Petugas loket untuk form penugasan
        $officers = User::query()->where('role', UserRole::AdminGerai->value)->get();

        return view('super_admin.gerai.index', compact(
            'totalDepartments',
            'totalStaff',
            'departments',
            'counters',
            'services',
            'officers'
        ));
    }
}
