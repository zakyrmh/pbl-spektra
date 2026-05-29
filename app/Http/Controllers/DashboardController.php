<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Department;
use App\Models\Queue as QueueModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;
        $role = $role instanceof \BackedEnum ? $role->value : ($role ?? 'pengunjung');
        if ($role === 'warga') {
            $role = 'pengunjung';
        }

        $data = [];

        if ($role === 'super_admin') {
            $today = Carbon::today()->toDateString();

            // 1. Total Kunjungan Hari Ini
            $todayKunjunganCount = QueueModel::query()->where('queue_date', $today)->count('*');
            $kunjunganPercentage = $this->calculateKunjunganPercentage($todayKunjunganCount, $today);

            // 2. Menunggu Konfirmasi FO (Booking online berstatus Pending hari ini)
            $menungguFoCount = Booking::query()->where('booking_date', $today)
                ->where('status', 'Pending')
                ->count('*');
            $foStatus = $this->getFoConfirmationStatus($menungguFoCount);

            // 3. Sedang Dilayani di Gerai (Total antrean aktif: Waiting + Serving)
            $waitingCount = QueueModel::query()->where('queue_date', $today)->where('status', 'Waiting')->count('*');
            $servingCount = QueueModel::query()->where('queue_date', $today)->where('status', 'Serving')->count('*');
            $totalAntreanGerai = $waitingCount + $servingCount;

            // 4. Total Gerai Aktif (Department yang memiliki minimal 1 loket aktif)
            $totalGerai = Department::query()->count('*');
            $activeGerai = Department::query()->whereHas('counters', function ($query) {
                $query->where('status', 'aktif');
            })->count('*');
            $geraiPercentage = $totalGerai > 0 ? (int) round(($activeGerai / $totalGerai) * 100) : 0;

            // 5. Data Live Tenant (untuk Tabel Pemantauan Live)
            $liveDepartments = Department::query()->with(['counters', 'queues' => function ($query) use ($today) {
                $query->where('queue_date', $today);
            }])->get();

            // 6. Live Activity Feed (dari tabel activity_logs)
            $liveLogs = ActivityLog::query()->latest()->take(5)->get();

            // 7. Data Grafik
            $chartTrenData = $this->getTrenKedatanganData($today);
            $chartTopTenantData = $this->getTopTenantData($today);

            $data = [
                'todayKunjunganCount' => $todayKunjunganCount,
                'kunjunganPercentage' => $kunjunganPercentage,
                'menungguFoCount' => $menungguFoCount,
                'foStatus' => $foStatus,
                'waitingCount' => $waitingCount,
                'servingCount' => $servingCount,
                'totalAntreanGerai' => $totalAntreanGerai,
                'totalGerai' => $totalGerai,
                'activeGerai' => $activeGerai,
                'geraiPercentage' => $geraiPercentage,
                'liveDepartments' => $liveDepartments,
                'liveLogs' => $liveLogs,
                'chartTrenData' => $chartTrenData,
                'chartTopTenantData' => $chartTopTenantData,
            ];
        }

        return view('dashboard.dashboard', $data);
    }

    /**
     * Menghitung persentase perubahan kunjungan hari ini terhadap rata-rata kunjungan harian historis.
     *
     * Analisis dibatasi pada 30 hari kalender terakhir (kemarin ke belakang) untuk menghindari
     * anomali persentase (+10.000%) saat sistem baru berjalan, dan agar query hanya memindai
     * rentang data yang terbatas dan bermakna secara statistik.
     */
    protected function calculateKunjunganPercentage(int $todayCount, string $today): array
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $pastDaysCount = QueueModel::query()
            ->where('queue_date', '>=', $thirtyDaysAgo)
            ->where('queue_date', '<=', $yesterday)
            ->count('*');

        $pastDaysUnique = QueueModel::query()
            ->where('queue_date', '>=', $thirtyDaysAgo)
            ->where('queue_date', '<=', $yesterday)
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
     * Menentukan tingkat kepadatan (status) antrean FO berdasarkan jumlah booking pending hari ini.
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
        $queuesToday = QueueModel::query()->where('queue_date', $today)->get();
        $hours = ['08', '09', '10', '11', '12', '13', '14', '15', '16'];
        $onlineData = [];
        $onsiteData = [];

        foreach ($hours as $h) {
            $onlineCount = $queuesToday->filter(function ($q) use ($h) {
                return Carbon::parse($q->created_at)->format('H') === $h && $q->booking_id !== null;
            })->count();

            $onsiteCount = $queuesToday->filter(function ($q) use ($h) {
                return Carbon::parse($q->created_at)->format('H') === $h && $q->booking_id === null;
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
     *
     * Return structure:
     *   - 'keys'   : array nama lengkap instansi (digunakan sebagai key mapping di JavaScript stats.queues)
     *   - 'labels' : array inisial singkat instansi (ditampilkan sebagai kategori pada grafik ApexCharts)
     *   - 'values' : array jumlah antrean (urutan sesuai dengan 'keys' dan 'labels')
     */
    protected function getTopTenantData(string $today): array
    {
        $departments = Department::all();
        $queuesToday = QueueModel::query()->where('queue_date', $today)->with('counter')->get();

        $data = [];
        foreach ($departments as $dept) {
            $count = $queuesToday->filter(function ($q) use ($dept) {
                return $q->counter && $q->counter->department_id === $dept->id;
            })->count();

            // Pisahkan key (nama lengkap, untuk JS) dari label (inisial singkat, untuk chart)
            $data[] = [
                'key' => $dept->name,
                'label' => $dept->inisial ?: substr($dept->name, 0, 6),
                'value' => $count,
            ];
        }

        // Sort descending berdasarkan jumlah antrean
        usort($data, fn ($a, $b) => $b['value'] <=> $a['value']);
        $top5 = array_slice($data, 0, 5);

        if (empty($top5)) {
            $top5 = [
                ['key' => 'Dinas Kesehatan',  'label' => 'DK', 'value' => 0],
                ['key' => 'Imigrasi',         'label' => 'IM', 'value' => 0],
                ['key' => 'Samsat',           'label' => 'SM', 'value' => 0],
                ['key' => 'BPN',              'label' => 'BP', 'value' => 0],
                ['key' => 'BPKP',             'label' => 'BK', 'value' => 0],
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
        return view('dashboard.dashboard'); // Sementara redirect ke dashboard
    }

    public function callNext(Request $request)
    {
        // TODO: Implementasi panggil antrean berikutnya
        return back()->with('success', 'Antrean berikutnya telah dipanggil.');
    }
}
