<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\DashboardAnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     */
    public function __construct(
        protected DashboardAnalyticsService $analyticsService
    ) {}

    /**
     * Tampilkan dashboard Pengunjung.
     */
    public function index(): View
    {
        $user = Auth::user();
        $data = $this->analyticsService->getVisitorDashboardData($user, Carbon::today()->toDateString())->toArray();

        return view('dashboard.dashboard', $data);
    }
}
