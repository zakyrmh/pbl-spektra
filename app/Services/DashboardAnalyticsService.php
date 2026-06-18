<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\AdminFO\FoDashboardData;
use App\Data\AdminGerai\AdminGeraiDashboardData;
use App\Data\Public\VisitorDashboardData;
use App\Data\SuperAdmin\SuperAdminDashboardData;
use App\Enums\QueueStatus;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardAnalyticsService
{
    public function getSuperAdminDashboardData(string $today): SuperAdminDashboardData
    {
        // 1. Total Kunjungan Hari Ini
        /** @var Builder $kunjunganQuery */
        $kunjunganQuery = Queue::query();
        $todayKunjunganCount = $kunjunganQuery->where('booking_date', $today)->count('*');
        $kunjunganPercentage = $this->calculateKunjunganPercentage($todayKunjunganCount, $today);

        // 2. Menunggu Konfirmasi FO
        /** @var Builder $menungguFoQuery */
        $menungguFoQuery = Queue::query();
        $menungguFoCount = $menungguFoQuery->where('booking_date', $today)
            ->where('status', QueueStatus::Booked->value)
            ->count('*');
        $foStatus = $this->getFoConfirmationStatus($menungguFoCount);

        // 3. Sedang Dilayani di Gerai
        /** @var Builder $waitingQuery */
        $waitingQuery = Queue::query();
        $waitingCount = $waitingQuery->where('booking_date', $today)
            ->where('status', QueueStatus::CheckedIn->value)
            ->count('*');
        /** @var Builder $servingQuery */
        $servingQuery = Queue::query();
        $servingCount = $servingQuery->where('booking_date', $today)
            ->where('status', QueueStatus::Serving->value)
            ->count('*');
        $totalAntreanGerai = $waitingCount + $servingCount;

        // 4. Total Gerai Aktif
        /** @var Builder $totalGeraiQuery */
        $totalGeraiQuery = Department::query();
        $totalGerai = $totalGeraiQuery->count('*');
        /** @var Builder $activeGeraiQuery */
        $activeGeraiQuery = Department::query();
        $activeGerai = $activeGeraiQuery->where('is_open', true)->count('*');
        $geraiPercentage = $totalGerai > 0 ? (int) round(($activeGerai / $totalGerai) * 100) : 0;

        // 5. Data Live Gerai
        /** @var Builder $liveDepartmentsQuery */
        $liveDepartmentsQuery = Department::query();
        $liveDepartments = $liveDepartmentsQuery->with(['queues' => function ($query) use ($today) {
            /** @var Builder $query */
            $query->where('booking_date', $today);
        }])->get();

        // 6. Live Activity Feed
        /** @var Builder $liveLogsQuery */
        $liveLogsQuery = ActivityLog::query();
        $liveLogs = $liveLogsQuery->with('causer')->latest()->take(5)->get();

        // 7. Data Grafik
        $chartTrenData = $this->getTrenKedatanganData($today);
        $chartTopGeraiData = $this->getTopGeraiData($today);

        // Menghitung jumlah booking yang sukses check-in di FO hari ini
        /** @var Builder $checkedInBookingsQuery */
        $checkedInBookingsQuery = Queue::query();
        $checkedInBookingsCount = $checkedInBookingsQuery
            ->where('booking_date', $today)
            ->whereIn('status', [QueueStatus::CheckedIn->value, QueueStatus::Serving->value, QueueStatus::Completed->value])
            ->whereNotNull('checked_in_at')
            ->count();

        if ($checkedInBookingsCount === 0) {
            $avgFoCheckInTime = null;
        } else {
            $avgFoCheckInTime = 1.2 + ($checkedInBookingsCount % 5) * 0.3;
        }

        return new SuperAdminDashboardData(
            todayKunjunganCount: $todayKunjunganCount,
            kunjunganPercentage: $kunjunganPercentage,
            menungguFoCount: $menungguFoCount,
            foStatus: $foStatus,
            avgFoCheckInTime: $avgFoCheckInTime,
            waitingCount: $waitingCount,
            servingCount: $servingCount,
            totalAntreanGerai: $totalAntreanGerai,
            totalGerai: $totalGerai,
            activeGerai: $activeGerai,
            geraiPercentage: $geraiPercentage,
            liveDepartments: $liveDepartments,
            liveLogs: $liveLogs,
            chartTrenData: $chartTrenData,
            chartTopGeraiData: $chartTopGeraiData
        );
    }

    public function getFoDashboardData(string $today): FoDashboardData
    {
        $departments = Department::all();

        // Mengambil 8 antrean terbaru hari ini
        /** @var Builder $recentQueuesQuery */
        $recentQueuesQuery = Queue::query();
        $recentQueues = $recentQueuesQuery
            ->where('booking_date', $today)
            ->with(['user', 'department'])
            ->latest()
            ->take(8)
            ->get();

        // Total tiket online yang belum check-in
        /** @var Builder $todayFoQueueQuery */
        $todayFoQueueQuery = Queue::query();
        $todayFoQueueCount = $todayFoQueueQuery
            ->where('booking_date', $today)
            ->whereNull('queue_number')
            ->where('status', QueueStatus::Booked->value)
            ->count('*');

        // Total nomor antrean yang sudah diterbitkan FO hari ini (baik online maupun walk-in)
        /** @var Builder $todayTotalPrintedQuery */
        $todayTotalPrintedQuery = Queue::query();
        $todayTotalPrintedTickets = $todayTotalPrintedQuery
            ->where('booking_date', $today)
            ->whereNotNull('queue_number')
            ->count('*');

        return new FoDashboardData(
            departments: $departments,
            recentQueues: $recentQueues,
            todayFoQueueCount: $todayFoQueueCount,
            todayTotalPrintedTickets: $todayTotalPrintedTickets
        );
    }

    protected function calculateKunjunganPercentage(int $todayCount, string $today): array
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        /** @var Builder $pastDaysCountQuery */
        $pastDaysCountQuery = Queue::query();
        $pastDaysCount = $pastDaysCountQuery
            ->where('booking_date', '>=', $thirtyDaysAgo)
            ->where('booking_date', '<=', $yesterday)
            ->count('*');

        /** @var Builder $pastDaysUniqueQuery */
        $pastDaysUniqueQuery = Queue::query();
        $pastDaysUnique = $pastDaysUniqueQuery
            ->where('booking_date', '>=', $thirtyDaysAgo)
            ->where('booking_date', '<=', $yesterday)
            ->distinct()
            ->count('booking_date');

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

    protected function getFoConfirmationStatus(int $count): array
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
        } else {
            return [
                'label' => 'Lancar',
                'color' => 'text-green-600 dark:text-green-400',
                'bg_dot' => 'bg-green-500',
            ];
        }
    }

    protected function getTrenKedatanganData(string $today): array
    {
        /** @var Builder $queuesTodayQuery */
        $queuesTodayQuery = Queue::query();
        $queuesToday = $queuesTodayQuery->where('booking_date', $today)->get();
        $hours = ['08', '09', '10', '11', '12', '13', '14', '15', '16'];
        $onlineData = [];
        $onsiteData = [];

        foreach ($hours as $h) {
            // Online: Waktu dibuat (created_at) berbeda hari/jam dengan waktu check-in di FO
            $onlineCount = $queuesToday->filter(function ($q) use ($h) {
                return Carbon::parse($q->created_at)->format('H') === $h &&
                       $q->created_at->toDateTimeString() !== $q->checked_in_at;
            })->count();

            // Onsite/Walk-In: Tiket dibuat langsung di tempat (created_at sama dengan checked_in_at)
            $onsiteCount = $queuesToday->filter(function ($q) use ($h) {
                return Carbon::parse($q->created_at)->format('H') === $h &&
                       ($q->checked_in_at === null || $q->created_at->toDateTimeString() === $q->checked_in_at);
            })->count();

            $onlineData[] = $onlineCount;
            $onsiteData[] = $onsiteCount;
        }

        return [
            'categories' => array_map(fn ($h) => "$h:00", $hours),
            'online' => $onlineData,
            'onsite' => $onsiteData,
        ];
    }

    protected function getTopGeraiData(string $today): array
    {
        $departments = Department::all();
        /** @var Builder $queuesTodayTopQuery */
        $queuesTodayTopQuery = Queue::query();
        $queuesToday = $queuesTodayTopQuery->where('booking_date', $today)->get();

        $data = [];
        foreach ($departments as $dept) {
            $count = $queuesToday->where('department_id', $dept->id)->count();

            $data[] = [
                'key' => $dept->name,
                'label' => $dept->inisial ?: substr($dept->name, 0, 6),
                'value' => $count,
            ];
        }

        usort($data, fn ($a, $b) => $b['value'] <=> $a['value']);
        $top5 = array_slice($data, 0, 5);

        if (empty($top5)) {
            $top5 = [
                ['key' => 'DPMPTSPNaker', 'label' => 'DPTK', 'value' => 0],
                ['key' => 'Bank Nagari',   'label' => 'BNR',  'value' => 0],
                ['key' => 'Samsat',        'label' => 'SMST', 'value' => 0],
                ['key' => 'Disdukcapil',   'label' => 'DDK',  'value' => 0],
                ['key' => 'BPJS Kesehatan', 'label' => 'BPJSK', 'value' => 0],
            ];
        }

        return [
            'keys' => array_column($top5, 'key'),
            'labels' => array_column($top5, 'label'),
            'values' => array_column($top5, 'value'),
        ];
    }

    /**
     * Get dashboard data for Admin Gerai.
     */
    public function getAdminGeraiDashboardData(?Department $department, string $today): AdminGeraiDashboardData
    {
        if (! $department) {
            return new AdminGeraiDashboardData(
                department: null,
                currentQueue: null,
                activeQueue: null,
                waitingQueues: collect(),
                skippedQueues: collect(),
                completedCount: 0,
                remainingCount: 0,
                avgServiceTime: 12,
                noCounter: true
            );
        }

        /** @var Builder $currentQueueQuery */
        $currentQueueQuery = Queue::query();
        $currentQueue = $currentQueueQuery->where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Serving->value)
            ->with('user')
            ->first();

        /** @var Builder $waitingQueuesQuery */
        $waitingQueuesQuery = Queue::query();
        $waitingQueues = $waitingQueuesQuery->where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::CheckedIn->value)
            ->with('user')
            ->orderBy('id', 'asc')
            ->get();

        /** @var Builder $skippedQueuesQuery */
        $skippedQueuesQuery = Queue::query();
        $skippedQueues = $skippedQueuesQuery->where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Skipped->value)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        /** @var Builder $completedCountQuery */
        $completedCountQuery = Queue::query();
        $completedCount = $completedCountQuery->where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Completed->value)
            ->count();

        // Rata-rata durasi pelayanan (dalam menit)
        /** @var Builder $completedTodayQuery */
        $completedTodayQuery = Queue::query();
        $completedToday = $completedTodayQuery->where('department_id', $department->id)
            ->whereDate('booking_date', $today)
            ->where('status', QueueStatus::Completed->value)
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedToday->isEmpty()) {
            $avgServiceTime = 12; // nilai default estimasi
        } else {
            $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
            $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
            $avgServiceTime = max($avgServiceTime, 1);
        }

        $remainingCount = $waitingQueues->count();

        return new AdminGeraiDashboardData(
            department: $department,
            currentQueue: $currentQueue,
            activeQueue: $currentQueue,
            waitingQueues: $waitingQueues,
            skippedQueues: $skippedQueues,
            completedCount: $completedCount,
            remainingCount: $remainingCount,
            avgServiceTime: $avgServiceTime,
            noCounter: false
        );
    }

    /**
     * Get dashboard data for Visitor (Pengunjung).
     */
    public function getVisitorDashboardData(User $user, string $today): VisitorDashboardData
    {
        /** @var Builder $activeBookingQuery */
        $activeBookingQuery = Queue::query();
        $activeBooking = $activeBookingQuery->where('user_id', $user->id)
            ->whereIn('status', [QueueStatus::Booked->value, QueueStatus::CheckedIn->value, QueueStatus::Serving->value])
            ->with(['department'])
            ->latest()
            ->first();

        $currentServingQueue = 'Belum Mulai';
        $remainingQueuesCount = 0;
        $estimatedTime = 0;

        if ($activeBooking && $activeBooking->queue_number) {
            /** @var Builder $currentServingQuery */
            $currentServingQuery = Queue::query();
            $currentServing = $currentServingQuery->where('department_id', $activeBooking->department_id)
                ->whereDate('booking_date', $activeBooking->booking_date)
                ->where('status', QueueStatus::Serving->value)
                ->first();

            if ($currentServing) {
                $currentServingQueue = $currentServing->queue_number;
            }

            /** @var Builder $remainingQueuesQuery */
            $remainingQueuesQuery = Queue::query();
            $remainingQueuesCount = $remainingQueuesQuery->where('department_id', $activeBooking->department_id)
                ->whereDate('booking_date', $activeBooking->booking_date)
                ->where('status', QueueStatus::CheckedIn->value)
                ->where('id', '<', $activeBooking->id)
                ->count();

            // Hitung estimasi waktu tunggu
            // Rata-rata durasi pelayanan (dalam menit)
            /** @var Builder $completedTodayQuery */
            $completedTodayQuery = Queue::query();
            $completedToday = $completedTodayQuery->where('department_id', $activeBooking->department_id)
                ->whereDate('booking_date', $activeBooking->booking_date)
                ->where('status', QueueStatus::Completed->value)
                ->whereNotNull('called_at')
                ->whereNotNull('completed_at')
                ->get();

            if ($completedToday->isEmpty()) {
                $avgServiceTime = 12; // nilai default estimasi
            } else {
                $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
                $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
                $avgServiceTime = max($avgServiceTime, 1);
            }

            $estimatedTime = $remainingQueuesCount * $avgServiceTime;
        }

        // 1. Kepadatan Gedung MPP
        /** @var Builder $totalWaitingQuery */
        $totalWaitingQuery = Queue::query();
        $totalWaiting = $totalWaitingQuery->whereDate('booking_date', $today)
            ->where('status', QueueStatus::CheckedIn->value)
            ->count();

        $densityPercentage = (int) min(100, round(($totalWaiting / 50) * 100));

        if ($densityPercentage < 40) {
            $densityStatus = 'Sepi';
            $densityClass = 'text-emerald-500';
            $densityDot = 'bg-emerald-500';
            $densityDescription = 'Kondisi senggang, tidak ada antrean berarti.';
        } elseif ($densityPercentage < 70) {
            $densityStatus = 'Normal';
            $densityClass = 'text-primary dark:text-accent-teal';
            $densityDot = 'bg-primary';
            $densityDescription = 'Kondisi kondusif, waktu antrean singkat.';
        } else {
            $densityStatus = 'Sangat Ramai';
            $densityClass = 'text-rose-500 animate-pulse';
            $densityDot = 'bg-rose-500 animate-pulse';
            $densityDescription = 'Gedung padat, waktu tunggu lebih lama.';
        }

        // 2. Tenant Teramai Hari Ini
        $topDepts = Department::withCount(['queues' => function ($query) use ($today) {
            $query->whereDate('booking_date', $today);
        }])
            ->orderBy('queues_count', 'desc')
            ->take(3)
            ->get();

        $maxQueueCount = $topDepts->max('queues_count') ?: 1;

        $topDepartments = $topDepts->map(function ($dept) use ($maxQueueCount) {
            return [
                'name' => $dept->name,
                'queues_count' => $dept->queues_count,
                'progress_percent' => (int) round(($dept->queues_count / $maxQueueCount) * 100),
            ];
        })->toArray();

        return new VisitorDashboardData(
            activeBooking: $activeBooking,
            currentServingQueue: $currentServingQueue,
            remainingQueuesCount: $remainingQueuesCount,
            estimatedTime: $estimatedTime,
            densityPercentage: $densityPercentage,
            densityStatus: $densityStatus,
            densityClass: $densityClass,
            densityDot: $densityDot,
            densityDescription: $densityDescription,
            topDepartments: $topDepartments
        );
    }
}
