<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Exports\QueuesExport;
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

class ReportController extends Controller
{
    // =========================================================================
    // FRONT OFFICE METHODS
    // =========================================================================

    /**
     * Tampilkan daftar laporan untuk Admin Front Office.
     */
    public function foIndex(): View
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403, 'Hanya Admin Front Office yang dapat mengelola laporan.');
        }

        $reports = Report::with('creator')->latest()->get();

        return view('admin.fo.reports.index', compact('reports'));
    }

    /**
     * Simpan laporan baru yang digenerate oleh FO.
     */
    public function foStore(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ], [
            'start_date.before_or_equal' => 'Tanggal mulai harus sebelum atau sama dengan tanggal akhir.',
            'end_date.before_or_equal' => 'Tanggal akhir tidak boleh melebihi hari ini.',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Cek data antrean dalam rentang tanggal tersebut
        $queues = Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->with('service.department')
            ->get();

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada rentang tanggal tersebut.');
        }

        $summary = $this->calculateSummary($startDate, $endDate, $queues);

        $report = Report::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data_summary' => $summary,
            'status' => 'Belum Dikirim',
        ]);

        AuditLogger::log(
            event: 'report_created',
            description: "Admin FO membuat rekap laporan baru: '{$report->title}' untuk periode {$startDate} s/d {$endDate}.",
            subject: $report
        );

        return redirect()->route('admin.fo.reports.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Update/generate ulang laporan yang belum dikirim.
     */
    public function foUpdate(Request $request, Report $report): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        if ($report->status === 'Terkirim') {
            return redirect()->route('admin.fo.reports.index')
                ->with('error', 'Laporan yang telah dikirim ke Super Admin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $queues = Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->with('service.department')
            ->get();

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada rentang tanggal tersebut untuk diperbarui.');
        }

        $summary = $this->calculateSummary($startDate, $endDate, $queues);

        $report->update([
            'title' => $validated['title'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data_summary' => $summary,
        ]);

        AuditLogger::log(
            event: 'report_updated',
            description: "Admin FO memperbarui rekap laporan: '{$report->title}' untuk periode {$startDate} s/d {$endDate}.",
            subject: $report
        );

        return redirect()->route('admin.fo.reports.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus draf laporan.
     */
    public function foDestroy(Report $report): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

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
     */
    public function foSend(Report $report): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        if ($report->status === 'Terkirim') {
            return redirect()->route('admin.fo.reports.index')
                ->with('warning', 'Laporan ini sudah dikirim sebelumnya.');
        }

        $report->update(['status' => 'Terkirim']);

        // Kirim notifikasi database ke semua Super Admin
        $superAdmins = User::where('role', UserRole::SuperAdmin->value)->get();
        foreach ($superAdmins as $sa) {
            Notification::create([
                'user_id' => $sa->id,
                'title' => 'Laporan Kinerja Baru',
                'message' => "Petugas FO telah mengirimkan Laporan Kinerja: {$report->title} untuk periode {$report->start_date->toDateString()} s/d {$report->end_date->toDateString()}.",
            ]);
        }

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
     */
    public function adminIndex(): View
    {
        if (Auth::user()->role !== UserRole::SuperAdmin) {
            abort(403, 'Akses khusus Super Administrator.');
        }

        $reports = Report::with('creator')
            ->where('status', 'Terkirim')
            ->latest()
            ->get();

        return view('super_admin.reports.index', compact('reports'));
    }

    /**
     * Tampilkan visualisasi analitik laporan secara detail.
     */
    public function adminShow(Report $report): View
    {
        if (Auth::user()->role !== UserRole::SuperAdmin) {
            abort(403);
        }

        if ($report->status !== 'Terkirim') {
            abort(404, 'Laporan belum dikirim oleh FO.');
        }

        // Ambil daftar detail riwayat kunjungan antrean
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
     * Ekspor daftar riwayat antrean laporan ke Excel.
     */
    public function exportExcel(Report $report)
    {
        if (Auth::user()->role !== UserRole::SuperAdmin) {
            abort(403);
        }

        $filename = 'rekap-kunjungan-mpp-'.$report->start_date->toDateString().'-to-'.$report->end_date->toDateString().'.xlsx';

        return Excel::download(new QueuesExport($report->start_date->toDateString(), $report->end_date->toDateString()), $filename);
    }

    /**
     * Ekspor riwayat antrean laporan ke file PDF.
     */
    public function exportPdf(Report $report)
    {
        if (Auth::user()->role !== UserRole::SuperAdmin) {
            abort(403);
        }

        $queues = Queue::whereDate('queue_date', '>=', $report->start_date)
            ->whereDate('queue_date', '<=', $report->end_date)
            ->where('status', 'Completed')
            ->with(['visitor', 'booking.user', 'service.department'])
            ->orderBy('queue_date')
            ->orderBy('queue_number')
            ->get();

        $pdf = Pdf::loadView('super_admin.reports.pdf', compact('report', 'queues'));
        $pdf->setPaper('a4', 'landscape');

        $filename = 'laporan-antrean-'.$report->start_date->toDateString().'-to-'.$report->end_date->toDateString().'.pdf';

        return $pdf->download($filename);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Hitung rekapitulasi agregat antrean.
     */
    protected function calculateSummary(string $startDate, string $endDate, $queues): array
    {
        $totalVisitors = $queues->count();
        $completedCount = $queues->where('status', 'Completed')->count();
        $skippedCount = $queues->where('status', 'Skipped')->count();

        // Rata-rata waktu tunggu & pelayanan
        $completedQueues = $queues->where('status', 'Completed');
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

        $avgServiceTime = $serviceCount > 0 ? round($totalServiceTime / $serviceCount, 1) : 0;
        $avgWaitingTime = $waitingCount > 0 ? round($totalWaitingTime / $waitingCount, 1) : 0;
        $attendanceRate = $totalVisitors > 0 ? round(($completedCount / $totalVisitors) * 100, 1) : 0;

        // Metrik Per Instansi / Gerai (Department)
        $perDepartment = [];
        $departments = Department::all();

        foreach ($departments as $dept) {
            $deptQueues = $queues->filter(function ($q) use ($dept) {
                return $q->service && $q->service->department_id === $dept->id;
            });

            if ($deptQueues->isEmpty()) {
                continue;
            }

            $dTotal = $deptQueues->count();
            $dCompleted = $deptQueues->where('status', 'Completed')->count();
            $dSkipped = $deptQueues->where('status', 'Skipped')->count();

            $dTotalService = 0;
            $dServiceCount = 0;
            $dTotalWaiting = 0;
            $dWaitingCount = 0;

            foreach ($deptQueues->where('status', 'Completed') as $q) {
                if ($q->called_at && $q->completed_at) {
                    $called = Carbon::parse($q->called_at);
                    $completed = Carbon::parse($q->completed_at);
                    if ($completed->greaterThanOrEqualTo($called)) {
                        $dTotalService += $completed->diffInMinutes($called, true);
                        $dServiceCount++;
                    }
                }
                if ($q->created_at && $q->called_at) {
                    $created = Carbon::parse($q->created_at);
                    $called = Carbon::parse($q->called_at);
                    if ($called->greaterThanOrEqualTo($created)) {
                        $dTotalWaiting += $called->diffInMinutes($created, true);
                        $dWaitingCount++;
                    }
                }
            }

            $dAvgService = $dServiceCount > 0 ? round($dTotalService / $dServiceCount, 1) : 0;
            $dAvgWaiting = $dWaitingCount > 0 ? round($dTotalWaiting / $dWaitingCount, 1) : 0;

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

        // Deret tren harian untuk grafik
        $dailySeries = [];
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayQueues = $queues->filter(function ($q) use ($dateStr) {
                $qDate = $q->queue_date instanceof Carbon ? $q->queue_date->toDateString() : (string) $q->queue_date;

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

    // =========================================================================
    // RESOURCE METHODS FOR BACKWARD COMPATIBILITY / VANI'S TESTS
    // =========================================================================

    /**
     * Tampilkan daftar laporan (Vani's Route).
     */
    public function index(): View
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }
        $reports = Report::with('creator')->latest()->get();

        return view('reports.index', compact('reports'));
    }

    /**
     * Tampilkan form buat laporan (Vani's Route).
     */
    public function create(): View
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        return view('reports.create');
    }

    /**
     * Simpan laporan baru (Vani's Route).
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $queues = Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->with('service.department')
            ->get();

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada tanggal tersebut.');
        }

        $summary = $this->calculateSummary($startDate, $endDate, $queues);

        $report = Report::create([
            'created_by' => Auth::id(),
            'title' => $validated['title'] ?? 'Laporan Kinerja Periode '.$startDate.' s/d '.$endDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data_summary' => $summary,
            'status' => 'Belum Dikirim',
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Tampilkan form edit laporan (Vani's Route).
     */
    public function edit(Report $report)
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        if ($report->status === 'Terkirim') {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
        }

        return view('reports.edit', compact('report'));
    }

    /**
     * Update laporan (Vani's Route).
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        if ($report->status === 'Terkirim') {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $queues = Queue::whereDate('queue_date', '>=', $startDate)
            ->whereDate('queue_date', '<=', $endDate)
            ->with('service.department')
            ->get();

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada tanggal tersebut.');
        }

        $summary = $this->calculateSummary($startDate, $endDate, $queues);

        $report->update([
            'title' => $validated['title'] ?? $report->title,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data_summary' => $summary,
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Hapus laporan (Vani's Route).
     */
    public function destroy(Report $report): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        if ($report->status === 'Terkirim') {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Kirim laporan ke Super Admin (Vani's Route).
     */
    public function send(Report $report): RedirectResponse
    {
        if (Auth::user()->role !== UserRole::AdminFo) {
            abort(403);
        }

        if ($report->status === 'Terkirim') {
            return redirect()->route('reports.index')
                ->with('warning', 'Laporan ini sudah dikirim sebelumnya.');
        }

        $report->update(['status' => 'Terkirim']);

        $superAdmins = User::where('role', UserRole::SuperAdmin->value)->get();
        foreach ($superAdmins as $sa) {
            Notification::create([
                'user_id' => $sa->id,
                'title' => 'Laporan Baru Masuk',
                'message' => "Petugas FO telah mengirimkan Laporan Kinerja: {$report->title}.",
            ]);
        }

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dikirim.');
    }
}
