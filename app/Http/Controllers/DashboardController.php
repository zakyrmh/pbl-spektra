<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\CallNextQueueRequest;
use App\Services\DashboardAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DashboardController — Role-based dispatcher untuk MPP Kota Sawahlunto.
 */
final class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     */
    public function __construct(
        protected DashboardAnalyticsService $analyticsService
    ) {}

    /**
     * Tampilkan halaman dashboard utama berdasarkan role pengguna.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $role = $user->role;

        // Resolving BackedEnum or string representation of UserRole
        $roleValue = $role instanceof \BackedEnum ? $role : UserRole::tryFrom((string) $role);

        // Menyelaraskan role warga ke pengunjung
        if ($roleValue === null || $role === 'warga') {
            $roleValue = UserRole::Pengunjung;
        }

        $data = [];
        $today = Carbon::today()->toDateString();

        if ($roleValue === UserRole::SuperAdmin) {
            $data = $this->analyticsService->getSuperAdminDashboardData($today)->toArray();
        } elseif ($roleValue === UserRole::AdminFo) {
            $data = $this->analyticsService->getFoDashboardData($today)->toArray();
        } elseif ($roleValue === UserRole::AdminGerai) {
            $data = $this->analyticsService->getAdminGeraiDashboardData($user->department, $today)->toArray();
        } elseif ($roleValue === UserRole::Pengunjung) {
            $data = $this->analyticsService->getVisitorDashboardData($user, $today)->toArray();
        }

        return view('dashboard.dashboard', $data);
    }

    /**
     * Halaman manajemen antrean.
     */
    public function manageQueue(): View
    {
        return view('dashboard.dashboard');
    }

    /**
     * Panggil antrean berikutnya.
     */
    public function callNext(CallNextQueueRequest $request): RedirectResponse
    {
        return back()->with('success', 'Antrean berikutnya telah dipanggil.');
    }
}
