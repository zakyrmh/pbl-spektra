<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Public\PublicDashboardService;
use Illuminate\Contracts\View\View;

final class PublicController extends Controller
{
    public function __construct(
        protected PublicDashboardService $dashboardService
    ) {}

    /**
     * Display the public landing page.
     */
    public function index(): View
    {
        $stats = $this->dashboardService->getLandingStats();

        return view('pages.index', compact('stats'));
    }

    /**
     * Display the check queue page.
     */
    public function checkQueue(): View
    {
        return view('pages.check-queue');
    }
}
