<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Counter;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan daftar laporan untuk Admin FO.
     * GET /fo/laporan
     */
    public function index(): View
    {
        $reports = Report::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    /**
     * Halaman form pembuatan laporan baru.
     * GET /fo/laporan/create
     */
    public function create(): View
    {
        return view('reports.create');
    }

    /**
     * Hitung agregat antrean dan simpan laporan dengan status 'Belum Dikirim'.
     * POST /fo/laporan
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Pindai data antrean dalam rentang tanggal
        $queues = Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->get();

        // 1. Cek apakah ada data antrean
        if ($queues->isEmpty()) {
            return back()->withInput()
                ->with('error', 'Tidak ada data antrean pada tanggal tersebut.');
        }

        $summary = $this->generateSummary($queues, $startDate, $endDate);

        $report = Report::create([
            'created_by' => Auth::id(),
            'title' => 'Laporan Pelayanan MPP ('.Carbon::parse($startDate)->format('d M Y').' - '.Carbon::parse($endDate)->format('d M Y').')',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data_summary' => $summary,
            'status' => 'Belum Dikirim',
        ]);

        ActivityLog::record(
            action: 'GENERATE_REPORT',
            modelType: 'Report',
            modelId: $report->id,
            description: "Front Office men-generate laporan baru periode {$startDate} sampai {$endDate}.",
            actorUserId: Auth::id()
        );

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Laporan berhasil dibuat. Silakan periksa preview di bawah.');
    }

    /**
     * Tampilkan preview/detail laporan untuk FO.
     * GET /fo/laporan/{report}
     */
    public function show(Report $report): View
    {
        return view('reports.show', compact('report'));
    }

    /**
     * Form edit rentang tanggal laporan.
     * GET /fo/laporan/{report}/edit
     */
    public function edit(Report $report): View|RedirectResponse
    {
        if ($report->isLocked()) {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
        }

        return view('reports.edit', compact('report'));
    }

    /**
     * Update rentang tanggal laporan dan hitung ulang agregasi.
     * PUT /fo/laporan/{report}
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        if ($report->isLocked()) {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
        }

        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $queues = Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->get();

        if ($queues->isEmpty()) {
            return back()->withInput()
                ->with('error', 'Data Kosong, Gagal Perbarui.');
        }

        $summary = $this->generateSummary($queues, $startDate, $endDate);

        $report->update([
            'title' => 'Laporan Pelayanan MPP ('.Carbon::parse($startDate)->format('d M Y').' - '.Carbon::parse($endDate)->format('d M Y').')',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data_summary' => $summary,
        ]);

        ActivityLog::record(
            action: 'UPDATE_REPORT',
            modelType: 'Report',
            modelId: $report->id,
            description: "Front Office memperbarui laporan periode {$startDate} sampai {$endDate}.",
            actorUserId: Auth::id()
        );

        return redirect()->route('reports.show', $report->id)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus laporan dari database (hanya jika belum dikirim).
     * DELETE /fo/laporan/{report}
     */
    public function destroy(Report $report): RedirectResponse
    {
        if ($report->isLocked()) {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
        }

        ActivityLog::record(
            action: 'DELETE_REPORT',
            modelType: 'Report',
            modelId: $report->id,
            description: "Front Office menghapus draft laporan periode {$report->start_date->toDateString()} sampai {$report->end_date->toDateString()}.",
            actorUserId: Auth::id()
        );

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Kirim laporan ke Super Admin (Kunci status & kirim notifikasi).
     * POST /fo/laporan/{report}/send
     */
    public function send(Report $report): RedirectResponse
    {
        if ($report->isLocked()) {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim sebelumnya.');
        }

        $report->update(['status' => 'Terkirim']);

        // Kirim notifikasi sistem ke seluruh Super Admin
        $superAdmins = User::where('role', 'super_admin')->get();
        foreach ($superAdmins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => 'Laporan Baru Masuk',
                'message' => "Laporan pelayanan baru '{$report->title}' telah dikirim oleh Front Office. Silakan tinjau rekap kinerjanya.",
            ]);
        }

        ActivityLog::record(
            action: 'SEND_REPORT',
            modelType: 'Report',
            modelId: $report->id,
            description: "Front Office mengirimkan laporan periode {$report->start_date->toDateString()} sampai {$report->end_date->toDateString()} ke Super Admin.",
            actorUserId: Auth::id()
        );

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dikirim.');
    }

    /**
     * Tampilkan daftar laporan masuk untuk Super Admin.
     * GET /admin/laporan
     */
    public function superAdminIndex(): View
    {
        $reports = Report::with('creator')
            ->where('status', 'Terkirim')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('super_admin.reports.index', compact('reports'));
    }

    /**
     * Tampilkan detail visual laporan untuk Super Admin.
     * GET /admin/laporan/{report}
     */
    public function superAdminShow(Report $report): View
    {
        if ($report->status !== 'Terkirim') {
            abort(403, 'Anda hanya dapat meninjau laporan yang telah dikirim.');
        }

        return view('super_admin.reports.show', compact('report'));
    }

    /**
     * Helper internal untuk memproses agregasi data antrean.
     */
    protected function generateSummary($queues, $startDate, $endDate): array
    {
        $totalVisitors = $queues->count();
        $completedCount = $queues->where('status', 'Completed')->count();
        $skippedCount = $queues->where('status', 'Skipped')->count();
        $waitingCount = $queues->where('status', 'Waiting')->count();
        $servingCount = $queues->where('status', 'Serving')->count();

        // Hitung rata-rata durasi pelayanan Completed
        $completedQueues = $queues->where('status', 'Completed');
        $totalSeconds = 0;
        $validDurCount = 0;
        foreach ($completedQueues as $q) {
            $duration = $q->calculateDuration();
            if ($duration !== null) {
                $totalSeconds += $duration;
                $validDurCount++;
            }
        }
        $avgServiceTime = $validDurCount > 0 ? (int) round($totalSeconds / $validDurCount) : 0;

        // Agregasi per loket/counter
        $countersData = [];
        $counters = Counter::with('department')->get();
        foreach ($counters as $c) {
            $cQueues = $queues->where('counter_id', $c->id);
            if ($cQueues->isEmpty()) {
                continue;
            }

            $cCompleted = $cQueues->where('status', 'Completed')->count();
            $cSkipped = $cQueues->where('status', 'Skipped')->count();
            $cWaiting = $cQueues->where('status', 'Waiting')->count();
            $cServing = $cQueues->where('status', 'Serving')->count();

            $cTotalSec = 0;
            $cValCount = 0;
            foreach ($cQueues->where('status', 'Completed') as $q) {
                $dur = $q->calculateDuration();
                if ($dur !== null) {
                    $cTotalSec += $dur;
                    $cValCount++;
                }
            }
            $cAvgSec = $cValCount > 0 ? (int) round($cTotalSec / $cValCount) : 0;

            $countersData[] = [
                'counter_id' => $c->id,
                'counter_name' => $c->name,
                'department_name' => $c->department ? $c->department->name : 'Layanan Umum',
                'total' => $cQueues->count(),
                'completed' => $cCompleted,
                'skipped' => $cSkipped,
                'waiting' => $cWaiting,
                'serving' => $cServing,
                'average_service_time_seconds' => $cAvgSec,
            ];
        }

        // Agregasi harian untuk grafik
        $dailyStats = [];
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dateQueues = $queues->where('queue_date', $dateStr);

            $dailyStats[] = [
                'date' => $date->format('d M'),
                'total' => $dateQueues->count(),
                'completed' => $dateQueues->where('status', 'Completed')->count(),
                'skipped' => $dateQueues->where('status', 'Skipped')->count(),
            ];
        }

        return [
            'total_visitors' => $totalVisitors,
            'completed' => $completedCount,
            'skipped' => $skippedCount,
            'waiting' => $waitingCount,
            'serving' => $servingCount,
            'average_service_time_seconds' => $avgServiceTime,
            'counters' => $countersData,
            'daily' => $dailyStats,
        ];
    }
}
