<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PublicDashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class PublicController extends Controller
{
    public function __construct(
        private readonly PublicDashboardService $dashboardService,
    ) {}

    /**
     * Halaman landing page utama.
     *
     * GET /
     */
    public function index(Request $request): View
    {
        $stats = $this->dashboardService->getLandingStats();

        return view('pages.index', [
            'totalInstansi' => $stats->totalInstansi,
            'rataWaktuTunggu' => $stats->rataWaktuTunggu,
        ]);
    }
}
