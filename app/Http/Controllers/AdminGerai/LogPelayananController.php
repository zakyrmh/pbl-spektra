<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Data\AdminGerai\LogPelayananData;
use App\Enums\QueueStatus;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\AdminGerai\LogPelayananService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LogPelayananController extends Controller
{
    public function __construct(
        protected LogPelayananService $logService
    ) {}

    /**
     * Tampilkan halaman Log Pelayanan.
     * GET /admin/log-pelayanan
     */
    public function index(Request $request): View
    {
        $department = $this->resolveOperatorDepartment();

        $filters = $request->only(['start_date', 'end_date', 'status', 'search']);

        $paginatedQueues = $this->logService->getPaginatedLogs($department, $filters);

        // Map to DTOs for strict type-safe view binding
        $logs = $paginatedQueues->through(fn ($q) => LogPelayananData::fromModel($q));

        // Summary counts (always based on all-time for this department, not filtered)
        $totalSuccess = $this->logService->countByStatus($department, QueueStatus::Completed->value);
        $totalSkipped = $this->logService->countByStatus($department, QueueStatus::Skipped->value);

        return view('admin.log-pelayanan', compact(
            'department',
            'logs',
            'totalSuccess',
            'totalSkipped',
            'filters',
        ));
    }

    /**
     * Ekspor data Log Pelayanan ke CSV.
     * GET /admin/log-pelayanan/export
     */
    public function export(Request $request): StreamedResponse
    {
        $department = $this->resolveOperatorDepartment();

        $filters = $request->only(['start_date', 'end_date', 'status', 'search']);

        $filename = 'log-pelayanan-'.$department->inisial.'-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Retrieve callback from Service
        $callback = $this->logService->getCsvExportCallback($department, $filters);

        return response()->stream($callback, 200, $headers);
    }

    // ─────────────────────────────────────────────────────────────────────

    private function resolveOperatorDepartment(): Department
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        return Department::findOrFail($user->departments_id);
    }
}
