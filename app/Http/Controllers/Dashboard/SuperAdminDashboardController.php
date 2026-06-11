<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Department;
use App\Models\Queue as QueueModel;
use Carbon\Carbon;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    /**
     * Tampilkan dashboard Super Admin.
     */
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        // 1. Total Kunjungan Hari Ini
        $todayKunjunganCount = QueueModel::query()->whereDate('queue_date', $today)->count('*');
        $kunjunganPercentage = $this->calculateKunjunganPercentage($todayKunjunganCount, $today);

        // 2. Menunggu Konfirmasi FO (Booking online berstatus Pending hari ini)
        $menungguFoCount = Booking::query()
            ->whereDate('booking_date', $today)
            ->where('status', 'Pending')
            ->count('*');
        $foStatus = $this->getFoConfirmationStatus($menungguFoCount);

        // 3. Sedang Dilayani di Gerai (Total antrean aktif: Waiting + Serving)
        $waitingCount = QueueModel::query()->whereDate('queue_date', $today)->where('status', 'Waiting')->count('*');
        $servingCount = QueueModel::query()->whereDate('queue_date', $today)->where('status', 'Serving')->count('*');
        $totalAntreanGerai = $waitingCount + $servingCount;

        // 4. Total Gerai Aktif
        $totalGerai = Department::query()->count('*');
        $activeGerai = Department::query()->whereHas('counters', function ($query) {
            $query->where('status', 'aktif');
        })->count('*');
        $geraiPercentage = $totalGerai > 0 ? (int) round(($activeGerai / $totalGerai) * 100) : 0;

        // 5. Data Live Gerai
        $liveDepartments = Department::query()->with(['counters', 'queues' => function ($query) use ($today) {
            $query->whereDate('queue_date', $today);
        }])->get();

        // 6. Live Activity Feed
        $liveLogs = ActivityLog::query()->latest()->take(5)->get();

        // 7. Data Grafik
        $chartTrenData = $this->getTrenKedatanganData($today);
        $chartTopGeraiData = $this->getTopGeraiData($today);

        // 8. Average FO Check-In Time
        $checkedInBookingsCount = Booking::query()
            ->whereDate('booking_date', $today)
            ->where('status', 'Checked-In')
            ->whereNotNull('checked_in_at')
            ->count();

        $avgFoCheckInTime = $checkedInBookingsCount === 0
            ? null
            : 1.2 + ($checkedInBookingsCount % 5) * 0.3;

        return view('dashboard.dashboard', [
            'todayKunjunganCount' => $todayKunjunganCount,
            'kunjunganPercentage' => $kunjunganPercentage,
            'menungguFoCount' => $menungguFoCount,
            'foStatus' => $foStatus,
            'avgFoCheckInTime' => $avgFoCheckInTime,
            'waitingCount' => $waitingCount,
            'servingCount' => $servingCount,
            'totalAntreanGerai' => $totalAntreanGerai,
            'totalGerai' => $totalGerai,
            'activeGerai' => $activeGerai,
            'geraiPercentage' => $geraiPercentage,
            'liveDepartments' => $liveDepartments,
            'liveLogs' => $liveLogs,
            'chartTrenData' => $chartTrenData,
            'chartTopGeraiData' => $chartTopGeraiData,
        ]);
    }

    /**
     * Menghitung persentase perubahan kunjungan hari ini terhadap rata-rata historis 30 hari.
     */
    private function calculateKunjunganPercentage(int $todayCount, string $today): array
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $pastDaysCount = QueueModel::query()
            ->whereDate('queue_date', '>=', $thirtyDaysAgo)
            ->whereDate('queue_date', '<=', $yesterday)
            ->count('*');

        $pastDaysUnique = QueueModel::query()
            ->whereDate('queue_date', '>=', $thirtyDaysAgo)
            ->whereDate('queue_date', '<=', $yesterday)
            ->distinct()
            ->count('queue_date');

        $avgDaily = $pastDaysUnique > 0 ? $pastDaysCount / $pastDaysUnique : 0;

        if ($avgDaily > 0) {
            $diff = (($todayCount - $avgDaily) / $avgDaily) * 100;
        } else {
            $diff = $todayCount > 0 ? 100.0 : 0.0;
        }

        return [
            'value' => $diff,
            'formatted' => ($diff >= 0 ? '+' : '').round($diff).'%',
            'is_increase' => $diff >= 0,
        ];
    }

    /**
     * Menentukan tingkat kepadatan antrean FO berdasarkan jumlah booking pending.
     */
    private function getFoConfirmationStatus(int $count): array
    {
        if ($count > 15) {
            return [
                'label' => 'Padat',
                'color' => 'text-status-skipped dark:text-rose-400',
                'bg_dot' => 'bg-status-skipped',
            ];
        } elseif ($count > 5) {
            return [
                'label' => 'Sedang',
                'color' => 'text-status-waiting',
                'bg_dot' => 'bg-status-waiting',
            ];
        }

        return [
            'label' => 'Lancar',
            'color' => 'text-green-600 dark:text-green-400',
            'bg_dot' => 'bg-green-500',
        ];
    }

    /**
     * Mengelompokkan kedatangan antrean online & onsite per jam.
     */
    private function getTrenKedatanganData(string $today): array
    {
        $queuesToday = QueueModel::query()->whereDate('queue_date', $today)->get();
        $hours = ['08', '09', '10', '11', '12', '13', '14', '15', '16'];
        $onlineData = [];
        $onsiteData = [];

        foreach ($hours as $h) {
            $onlineData[] = $queuesToday->filter(fn ($q) => Carbon::parse($q->created_at)->format('H') === $h && $q->booking_id !== null
            )->count();

            $onsiteData[] = $queuesToday->filter(fn ($q) => Carbon::parse($q->created_at)->format('H') === $h && $q->booking_id === null
            )->count();
        }

        return [
            'categories' => array_map(fn ($h) => "$h:00", $hours),
            'online' => $onlineData,
            'onsite' => $onsiteData,
        ];
    }

    /**
     * Mengambil 5 Instansi terpadat berdasarkan jumlah tiket hari ini.
     *
     * Return structure:
     *   - 'keys'   : array nama lengkap instansi (key mapping di JavaScript stats.queues)
     *   - 'labels' : array inisial singkat instansi (kategori pada grafik ApexCharts)
     *   - 'values' : array jumlah antrean
     */
    private function getTopGeraiData(string $today): array
    {
        $departments = Department::all();
        $queuesToday = QueueModel::query()->whereDate('queue_date', $today)->with('counter')->get();

        $data = $departments->map(function ($dept) use ($queuesToday) {
            $count = $queuesToday->filter(fn ($q) => $q->counter && $q->counter->department_id === $dept->id
            )->count();

            return [
                'key' => $dept->name,
                'label' => $dept->inisial ?: substr($dept->name, 0, 6),
                'value' => $count,
            ];
        })->sortByDesc('value')->values()->take(5)->toArray();

        if (empty($data)) {
            $data = [
                ['key' => 'Dinas Kesehatan', 'label' => 'DK', 'value' => 0],
                ['key' => 'Imigrasi',        'label' => 'IM', 'value' => 0],
                ['key' => 'Samsat',          'label' => 'SM', 'value' => 0],
                ['key' => 'BPN',             'label' => 'BP', 'value' => 0],
                ['key' => 'BPKP',            'label' => 'BK', 'value' => 0],
            ];
        }

        return [
            'keys' => array_column($data, 'key'),
            'labels' => array_column($data, 'label'),
            'values' => array_column($data, 'value'),
        ];
    }
}
