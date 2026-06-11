<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard Admin Gerai (per-instansi).
     */
    public function index(): View
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $today = Carbon::today()->toDateString();

        // Fetch schedules today dengan eager loading service
        $schedules = Schedule::whereDate('date', $today)
            ->whereHas('service', fn ($q) => $q->where('department_id', $user->departments_id))
            ->with('service')
            ->get();

        // Cards statistics
        $totalAntrean = $schedules->sum('quota_used');

        $sisaAntrean = Booking::whereDate('booking_date', $today)
            ->whereHas('service', fn ($q) => $q->where('department_id', $user->departments_id))
            ->where('status', 'Checked-In')
            ->count();

        $suksesDilayani = Booking::whereDate('booking_date', $today)
            ->whereHas('service', fn ($q) => $q->where('department_id', $user->departments_id))
            ->where('status', 'Completed')
            ->count();

        $terlewat = Booking::whereDate('booking_date', $today)
            ->whereHas('service', fn ($q) => $q->where('department_id', $user->departments_id))
            ->where('status', 'Cancelled')
            ->count();

        // Chart data: hourly completed/cancelled bookings
        $chartTrenData = $this->buildChartData($today, $user->departments_id);

        $isStatsDashboard = true;
        $isGeraiOpen = $schedules->isEmpty() ? false : ($schedules->where('is_open', true)->count() > 0);

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

        $bookingsToday = Booking::whereDate('booking_date', $today)
            ->whereHas('service', fn ($q) => $q->where('department_id', $departmentId))
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->get();

        $chartSukses = [];
        $chartBatal = [];

        foreach ($hours as $hour) {
            $chartSukses[] = $bookingsToday->filter(fn ($b) => $b->status === 'Completed' && $b->updated_at && $b->updated_at->format('H') === $hour
            )->count();

            $chartBatal[] = $bookingsToday->filter(fn ($b) => $b->status === 'Cancelled' && $b->updated_at && $b->updated_at->format('H') === $hour
            )->count();
        }

        return [
            'categories' => array_map(fn ($h) => "$h:00", $hours),
            'sukses' => $chartSukses,
            'batal' => $chartBatal,
        ];
    }
}
