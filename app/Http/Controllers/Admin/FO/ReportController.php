<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Enums\UserRole;
use App\Exports\QueuesExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Report;
use App\Models\User;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * ReportController — Manajemen Laporan Kinerja.
 *
 * Akses dibagi berdasarkan role:
 *   - Admin FO  : membuat, mengedit, menghapus, mengirim laporan
 *   - Super Admin: melihat laporan masuk, export Excel/PDF
 *
 * Otorisasi dilindungi oleh middleware route (role:admin_fo / role:super_admin),
 * sehingga tidak perlu pengecekan manual di tiap method.
 */
class ReportController extends Controller
{
    // =========================================================================
    // FRONT OFFICE METHODS
    // =========================================================================

    /**
     * Tampilkan daftar laporan untuk Admin Front Office.
     * GET /fo/reports
     */
    public function foIndex(): View
    {
        $reports = Report::with('creator')->latest()->get();

        return view('admin.fo.reports.index', compact('reports'));
    }

    /**
     * Simpan laporan baru yang digenerate oleh FO.
     * POST /fo/reports
     */
    public function foStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'start_date.before_or_equal' => 'Tanggal mulai harus sebelum atau sama dengan tanggal akhir.',
            'end_date.before_or_equal' => 'Tanggal akhir tidak boleh melebihi hari ini.',
        ]);

        $queues = $this->getQueuesForRange($validated['start_date'], $validated['end_date']);

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada rentang tanggal tersebut.');
        }

        $report = Report::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'data_summary' => $this->calculateSummary($validated['start_date'], $validated['end_date'], $queues),
            'status' => 'Belum Dikirim',
        ]);

        AuditLogger::log(
            event: 'report_created',
            description: "Admin FO membuat rekap laporan baru: '{$report->title}' untuk periode {$validated['start_date']} s/d {$validated['end_date']}.",
            subject: $report
        );

        return redirect()->route('admin.fo.reports.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Update/generate ulang laporan yang belum dikirim.
     * PUT /fo/reports/{report}
     */
    public function foUpdate(Request $request, Report $report): RedirectResponse
    {
        if ($report->status === 'Terkirim') {
            return redirect()->route('admin.fo.reports.index')
                ->with('error', 'Laporan yang telah dikirim ke Super Admin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $queues = $this->getQueuesForRange($validated['start_date'], $validated['end_date']);

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada rentang tanggal tersebut untuk diperbarui.');
        }

        $report->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'data_summary' => $this->calculateSummary($validated['start_date'], $validated['end_date'], $queues),
        ]);

        AuditLogger::log(
            event: 'report_updated',
            description: "Admin FO memperbarui rekap laporan: '{$report->title}' untuk periode {$validated['start_date']} s/d {$validated['end_date']}.",
            subject: $report
        );

        return redirect()->route('admin.fo.reports.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus draf laporan.
     * DELETE /fo/reports/{report}
     */
    public function foDestroy(Report $report): RedirectResponse
    {
        if ($report->status === 'Terkirim') {
            return redirect()->route('admin.fo.reports.index')
                ->with('error', 'Laporan yang telah dikirim ke Super Admin tidak dapat dihapus.');
        }

        $title = $report->title;
        $report->delete();

        AuditLogger::log(
            event: 'report_deleted',
            description: "Admin FO menghapus draf laporan '{$title}'.",
            subject: $report
        );

        return redirect()->route('admin.fo.reports.index')
            ->with('success', "Laporan '{$title}' berhasil dihapus.");
    }

    /**
     * Kirim laporan ke Super Admin.
     * POST /fo/reports/{report}/send
     */
    public function foSend(Report $report): RedirectResponse
    {
        if ($report->status === 'Terkirim') {
            return redirect()->route('admin.fo.reports.index')
                ->with('warning', 'Laporan ini sudah dikirim sebelumnya.');
        }

        $report->update(['status' => 'Terkirim']);

        // Kirim notifikasi ke semua Super Admin
        User::where('role', UserRole::SuperAdmin->value)->each(function ($sa) use ($report) {
            Notification::create([
                'user_id' => $sa->id,
                'title' => 'Laporan Kinerja Baru',
                'message' => "Petugas FO telah mengirimkan Laporan Kinerja: {$report->title} untuk periode {$report->start_date->toDateString()} s/d {$report->end_date->toDateString()}.",
            ]);
        });

        AuditLogger::log(
            event: 'report_sent',
            description: "Admin FO mengirim laporan '{$report->title}' ke Super Admin.",
            subject: $report
        );

        return redirect()->route('admin.fo.reports.index')
            ->with('success', 'Laporan berhasil dikirim ke Super Admin.');
    }

    // =========================================================================
    // SUPER ADMIN METHODS
    // =========================================================================

    /**
     * Tampilkan daftar laporan masuk untuk Super Admin.
     * GET /laporan-analitik
     */
    public function adminIndex(): View
    {
        $reports = Report::with('creator')
            ->where('status', 'Terkirim')
            ->latest()
            ->get();

        return view('super_admin.reports.index', compact('reports'));
    }

    /**
     * Tampilkan visualisasi analitik laporan secara detail.
     * GET /laporan-analitik/{report}
     */
    public function adminShow(Report $report): View
    {
        if ($report->status !== 'Terkirim') {
            abort(404, 'Laporan belum dikirim oleh FO.');
        }

        $queues = Queue::whereDate('queue_date', '>=', $report->start_date)
            ->whereDate('queue_date', '<=', $report->end_date)
            ->where('status', 'Completed')
            ->with(['visitor', 'booking.user', 'service.department'])
            ->orderBy('queue_date')
            ->orderBy('queue_number')
            ->paginate(15);

        return view('super_admin.reports.show', compact('report', 'queues'));
    }

    /**
     * Ekspor riwayat antrean laporan ke Excel.
     * GET /laporan-analitik/{report}/export/excel
     */
    public function exportExcel(Report $report): mixed
    {
        $filename = 'rekap-kunjungan-mpp-'.$report->start_date->toDateString().'-to-'.$report->end_date->toDateString().'.xlsx';

        return Excel::download(
            new QueuesExport($report->start_date->toDateString(), $report->end_date->toDateString()),
            $filename
        );
    }

    /**
     * Ekspor riwayat antrean laporan ke PDF.
     * GET /laporan-analitik/{report}/export/pdf
     */
    public function exportPdf(Report $report): mixed
    {
        $queues = Queue::whereDate('queue_date', '>=', $report->start_date)
            ->whereDate('queue_date', '<=', $report->end_date)
            ->where('status', 'Completed')
            ->with(['visitor', 'booking.user', 'service.department'])
            ->orderBy('queue_date')
            ->orderBy('queue_number')
            ->get();

        $pdf = Pdf::loadView('super_admin.reports.pdf', compact('report', 'queues'));
        $filename = 'laporan-antrean-'.$report->start_date->toDateString().'-to-'.$report->end_date->toDateString().'.pdf';

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Ambil data antrean dalam rentang tanggal.
     */
    private function getQueuesForRange(string $startDate, string $endDate)
    {
        return Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->with('service.department')
            ->get();
    }

    /**
     * Hitung rekapitulasi agregat antrean.
     */
    private function calculateSummary(string $startDate, string $endDate, $queues): array
    {
        $totalVisitors = $queues->count();
        $completedCount = $queues->where('status', 'Completed')->count();
        $skippedCount = $queues->where('status', 'Skipped')->count();

        [$avgServiceTime, $avgWaitingTime] = $this->calculateAverageTimes($queues->where('status', 'Completed'));

        $attendanceRate = $totalVisitors > 0 ? round(($completedCount / $totalVisitors) * 100, 1) : 0;

        $perDepartment = $this->calculatePerDepartment($queues);
        $dailySeries = $this->buildDailySeries($startDate, $endDate, $queues);

        return [
            'total_visitors' => $totalVisitors,
            'completed_count' => $completedCount,
            'skipped_count' => $skippedCount,
            'attendance_rate' => $attendanceRate,
            'avg_service_time' => $avgServiceTime,
            'avg_waiting_time' => $avgWaitingTime,
            'per_department' => $perDepartment,
            'daily_series' => $dailySeries,
        ];
    }

    /**
     * Hitung rata-rata waktu pelayanan & tunggu dari collection queues completed.
     *
     * @return array{0: float, 1: float} [$avgServiceTime, $avgWaitingTime]
     */
    private function calculateAverageTimes($completedQueues): array
    {
        $totalServiceTime = 0;
        $serviceCount = 0;
        $totalWaitingTime = 0;
        $waitingCount = 0;

        foreach ($completedQueues as $q) {
            if ($q->called_at && $q->completed_at) {
                $called = Carbon::parse($q->called_at);
                $completed = Carbon::parse($q->completed_at);
                if ($completed->greaterThanOrEqualTo($called)) {
                    $totalServiceTime += $completed->diffInMinutes($called, true);
                    $serviceCount++;
                }
            }
            if ($q->created_at && $q->called_at) {
                $created = Carbon::parse($q->created_at);
                $called = Carbon::parse($q->called_at);
                if ($called->greaterThanOrEqualTo($created)) {
                    $totalWaitingTime += $called->diffInMinutes($created, true);
                    $waitingCount++;
                }
            }
        }

        return [
            $serviceCount > 0 ? round($totalServiceTime / $serviceCount, 1) : 0,
            $waitingCount > 0 ? round($totalWaitingTime / $waitingCount, 1) : 0,
        ];
    }

    /**
     * Hitung metrik per instansi/departemen.
     */
    private function calculatePerDepartment($queues): array
    {
        $perDepartment = [];

        foreach (Department::all() as $dept) {
            $deptQueues = $queues->filter(fn ($q) => $q->service && $q->service->department_id === $dept->id);

            if ($deptQueues->isEmpty()) {
                continue;
            }

            $dTotal = $deptQueues->count();
            $dCompleted = $deptQueues->where('status', 'Completed')->count();
            $dSkipped = $deptQueues->where('status', 'Skipped')->count();

            [$dAvgService, $dAvgWaiting] = $this->calculateAverageTimes($deptQueues->where('status', 'Completed'));

            $perDepartment[] = [
                'department_id' => $dept->id,
                'department_name' => $dept->name,
                'inisial' => $dept->inisial,
                'total_queues' => $dTotal,
                'completed_queues' => $dCompleted,
                'skipped_queues' => $dSkipped,
                'avg_service_time' => $dAvgService,
                'avg_waiting_time' => $dAvgWaiting,
            ];
        }

        return $perDepartment;
    }

    /**
     * Bangun deret tren harian.
     */
    private function buildDailySeries(string $startDate, string $endDate, $queues): array
    {
        $dailySeries = [];
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayQueues = $queues->filter(function ($q) use ($dateStr) {
                $qDate = $q->queue_date instanceof Carbon
                    ? $q->queue_date->toDateString()
                    : (string) $q->queue_date;

                return $qDate === $dateStr;
            });

            $dailySeries[] = [
                'date' => $dateStr,
                'formatted_date' => $date->format('d M'),
                'total' => $dayQueues->count(),
                'completed' => $dayQueues->where('status', 'Completed')->count(),
                'skipped' => $dayQueues->where('status', 'Skipped')->count(),
            ];
        }

        return $dailySeries;
    }
}
