<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\CallNextQueueRequest;
use App\Models\User;
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
        /** @var User $user */
        $user = Auth::user();

        // Menyelaraskan role warga ke pengunjung
        if ($user->hasRole(UserRole::SuperAdmin)) {
            $data = $this->analyticsService->getSuperAdminDashboardData(Carbon::today()->toDateString())->toArray();
        } elseif ($user->hasRole(UserRole::AdminFo)) {
            $data = $this->analyticsService->getFoDashboardData(Carbon::today()->toDateString())->toArray();
        } elseif ($user->hasRole(UserRole::AdminGerai)) {
            $data = $this->analyticsService->getAdminGeraiDashboardData($user->department, Carbon::today()->toDateString())->toArray();
        } else {
            $data = $this->analyticsService->getVisitorDashboardData($user, Carbon::today()->toDateString())->toArray();
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
