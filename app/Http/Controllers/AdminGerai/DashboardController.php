<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Statistics/analytics dashboard for Admin Gerai.
 *
 * NOTE: The legacy Schedule and Booking models are deleted.
 * Stats are now computed directly from the queues table.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard statistik Admin Gerai (per-instansi).
     */
    public function index(): View
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $departmentId = $user->departments_id;
        $today = Carbon::today()->toDateString();

        // Cards statistics — derived from queues table
        $totalAntrean = Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->count();

        $sisaAntrean = Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->where('status', 'Checked-In')
            ->count();

        $suksesDilayani = Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->where('status', 'Completed')
            ->count();

        $terlewat = Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['Cancelled', 'Skipped'])
            ->count();

        // Chart data: hourly completed/cancelled queues
        $chartTrenData = $this->buildChartData($today, $departmentId);

        // Schedule model is deleted — pass empty collection for the
        // @forelse fallback in the stats view (renders "Tidak ada jadwal...")
        $schedules = collect();
        $isGeraiOpen = (bool) Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['Checked-In', 'Serving'])
            ->exists();

        $isStatsDashboard = true;

        return view('dashboard.dashboard', compact(
            'schedules',
            'totalAntrean',
            'sisaAntrean',
            'suksesDilayani',
            'terlewat',
            'chartTrenData',
            'isStatsDashboard',
            'isGeraiOpen'
        ));
    }

    /**
     * Membangun data chart tren hourly untuk hari ini per departemen.
     */
    private function buildChartData(string $today, int $departmentId): array
    {
        $hours = ['08', '09', '10', '11', '12', '13', '14', '15', '16'];

        $queuesFinishedToday = Queue::where('department_id', $departmentId)
            ->whereDate('booking_date', $today)
            ->whereIn('status', ['Completed', 'Cancelled', 'Skipped'])
            ->get();

        $chartSukses = [];
        $chartBatal = [];

        foreach ($hours as $hour) {
            $chartSukses[] = $queuesFinishedToday
                ->filter(fn ($q) => $q->status === 'Completed'
                    && $q->completed_at
                    && $q->completed_at->format('H') === $hour)
                ->count();

            $chartBatal[] = $queuesFinishedToday
                ->filter(fn ($q) => in_array($q->status, ['Cancelled', 'Skipped'], true)
                    && $q->completed_at
                    && $q->completed_at->format('H') === $hour)
                ->count();
        }

        return [
            'categories' => array_map(fn ($h) => "{$h}:00", $hours),
            'sukses' => $chartSukses,
            'batal' => $chartBatal,
        ];
    }
}
