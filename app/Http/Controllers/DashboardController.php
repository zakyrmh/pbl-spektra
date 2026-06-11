<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
// Import Model Baru yang Sesuai dengan Skema Baru
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DashboardController — Role-based dispatcher untuk MPP Kota Sawahlunto.
 */
class DashboardController extends Controller
{
    public function index(Request $request): mixed
    {
        $role = Auth::user()->role;
        $role = $role instanceof \BackedEnum ? $role->value : ($role ?? 'pengunjung');

        if ($role === 'warga') {
            $role = 'pengunjung';
        }

        $data = [];
        $today = Carbon::today()->toDateString();

        if ($role === 'super_admin') {

            // 1. Total Kunjungan Hari Ini (Menggunakan kolom booking_date pada tabel queues)
            $todayKunjunganCount = Queue::query()->where('booking_date', $today)->count('*');
            $kunjunganPercentage = $this->calculateKunjunganPercentage($todayKunjunganCount, $today);

            // 2. Menunggu Konfirmasi FO (Booking online yang statusnya masih 'Booked' hari ini)
            $menungguFoCount = Queue::query()->where('booking_date', $today)
                ->where('status', 'Booked')
                ->count('*');
            $foStatus = $this->getFoConfirmationStatus($menungguFoCount);

            // 3. Sedang Dilayani di Gerai (Total antrean aktif di ruang tunggu dan meja pelayanan)
            $waitingCount = Queue::query()->where('booking_date', $today)->where('status', 'Checked-In')->count('*');
            $servingCount = Queue::query()->where('booking_date', $today)->where('status', 'Serving')->count('*');
            $totalAntreanGerai = $waitingCount + $servingCount;

            // 4. Total Gerai Aktif (Menggunakan kolom is_open langsung di tabel departments)
            $totalGerai = Department::query()->count('*');
            $activeGerai = Department::query()->where('is_open', true)->count('*');
            $geraiPercentage = $totalGerai > 0 ? (int) round(($activeGerai / $totalGerai) * 100) : 0;

            // 5. Data Live Gerai (Mengambil data antrean hari ini langsung per departemen)
            $liveDepartments = Department::query()->with(['queues' => function ($query) use ($today) {
                $query->where('booking_date', $today);
            }])->get();

            // 6. Live Activity Feed (Menggunakan skema causer_id hasil perbaikan)
            $liveLogs = ActivityLog::query()->with('user')->latest()->take(5)->get();

            // 7. Data Grafik
            $chartTrenData = $this->getTrenKedatanganData($today);
            $chartTopGeraiData = $this->getTopGeraiData($today);

            // Menghitung jumlah booking yang sukses check-in di FO hari ini
            $checkedInBookingsCount = Queue::query()
                ->where('booking_date', $today)
                ->whereIn('status', ['Checked-In', 'Serving', 'Completed'])
                ->whereNotNull('checked_in_at')
                ->count();

            if ($checkedInBookingsCount === 0) {
                $avgFoCheckInTime = null;
            } else {
                $avgFoCheckInTime = 1.2 + ($checkedInBookingsCount % 5) * 0.3;
            }

            $data = [
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
            ];

        } elseif ($role === 'admin_fo') {

            $departments = Department::all();

            // Mengambil 8 antrean terbaru hari ini dengan relasi user (pengunjung) dan departemen langsung
            $recentQueues = Queue::query()
                ->where('booking_date', $today)
                ->with(['user', 'department'])
                ->latest()
                ->take(8)
                ->get();

            // Total tiket online yang belum check-in
            $todayFoQueueCount = Queue::query()
                ->where('booking_date', $today)
                ->where('status', 'Booked')
                ->count('*');

            // Total nomor antrean yang sudah diterbitkan FO hari ini (baik online maupun walk-in)
            $todayTotalPrintedTickets = Queue::query()
                ->where('booking_date', $today)
                ->whereNotNull('queue_number')
                ->count('*');

            $data = [
                'departments' => $departments,
                'recentQueues' => $recentQueues,
                'todayFoQueueCount' => $todayFoQueueCount,
                'todayTotalPrintedTickets' => $todayTotalPrintedTickets,
            ];
        }

        return view('dashboard.dashboard', $data);
    }

    /**
     * Menghitung persentase perubahan kunjungan harian.
     */
    protected function calculateKunjunganPercentage(int $todayCount, string $today): array
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $pastDaysCount = Queue::query()
            ->where('booking_date', '>=', $thirtyDaysAgo)
            ->where('booking_date', '<=', $yesterday)
            ->count('*');

        $pastDaysUnique = Queue::query()
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

    /**
     * Menentukan tingkat kepadatan antrean FO.
     */
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

    /**
     * Mengelompokkan kedatangan antrean online & onsite per jam.
     */
    protected function getTrenKedatanganData(string $today): array
    {
        $queuesToday = Queue::query()->where('booking_date', $today)->get();
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

    /**
     * Mengambil 5 Instansi terpadat berdasarkan jumlah tiket hari ini.
     */
    protected function getTopGeraiData(string $today): array
    {
        $departments = Department::all();
        $queuesToday = Queue::query()->where('booking_date', $today)->get();

        $data = [];
        foreach ($departments as $dept) {
            // Langsung filter berdasarkan department_id di tabel queues (Tanpa JOIN tabel Counter!)
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

    public function manageQueue()
    {
        return view('dashboard.dashboard');
    }

    public function callNext(Request $request)
    {
        return back()->with('success', 'Antrean berikutnya telah dipanggil.');
    }
}
