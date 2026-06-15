<?php

declare(strict_types=1);

namespace App\Services\AdminFO;

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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportAnalyticsService
{
    /**
     * Ambil semua laporan untuk Front Office.
     */
    public function getAllReports(): Collection
    {
        return Report::with('creator')->latest()->get();
    }

    /**
     * Ambil laporan terkirim untuk Super Admin.
     */
    public function getSentReports(): Collection
    {
        return Report::with('creator')
            ->where('status', 'Terkirim')
            ->latest()
            ->get();
    }

    /**
     * Dapatkan antrean yang selesai dan terpaginasi untuk detail laporan.
     */
    public function getCompletedQueuesForReport(Report $report)
    {
        return Queue::whereDate('booking_date', '>=', $report->start_date)
            ->whereDate('booking_date', '<=', $report->end_date)
            ->where('status', 'Completed')
            ->with(['user', 'department'])
            ->orderBy('booking_date')
            ->orderBy('queue_number')
            ->paginate(15);
    }

    /**
     * Buat laporan baru.
     */
    public function createReport(array $data, Collection $queues): Report
    {
        $report = Report::create([
            'created_by' => Auth::id(),
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'data_summary' => $this->calculateSummary($data['start_date'], $data['end_date'], $queues),
            'status' => 'Belum Dikirim',
        ]);

        AuditLogger::log(
            event: 'report_created',
            description: "Admin FO membuat rekap laporan baru: '{$report->title}' untuk periode {$data['start_date']} s/d {$data['end_date']}.",
            subject: $report
        );

        return $report;
    }

    /**
     * Perbarui laporan.
     */
    public function updateReport(Report $report, array $data, Collection $queues): void
    {
        $report->update([
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'data_summary' => $this->calculateSummary($data['start_date'], $data['end_date'], $queues),
        ]);

        AuditLogger::log(
            event: 'report_updated',
            description: "Admin FO memperbarui rekap laporan: '{$report->title}' untuk periode {$data['start_date']} s/d {$data['end_date']}.",
            subject: $report
        );
    }

    /**
     * Hapus laporan.
     */
    public function deleteReport(Report $report): void
    {
        $title = $report->title;
        $report->delete();

        AuditLogger::log(
            event: 'report_deleted',
            description: "Admin FO menghapus draf laporan '{$title}'.",
            subject: $report
        );
    }

    /**
     * Kirim laporan.
     */
    public function sendReport(Report $report): void
    {
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
    }

    /**
     * Ambil data antrean dalam rentang tanggal.
     */
    public function getQueuesForRange(string $startDate, string $endDate): Collection
    {
        return Queue::whereDate('booking_date', '>=', $startDate)
            ->whereDate('booking_date', '<=', $endDate)
            ->with('department')
            ->get();
    }

    /**
     * Hitung rekapitulasi agregat antrean.
     */
    public function calculateSummary(string $startDate, string $endDate, Collection $queues): array
    {
        $totalVisitors = $queues->count();
        $completedCount = $queues->where('status', 'Completed')->count();
        $skippedCount = $queues->where('status', 'Skipped')->count();

        [$avgServiceTime, $avgWaitingTime] = $this->calculateAverageTimes($queues->where('status', 'Completed'));

        $attendanceRate = $totalVisitors > 0 ? round(($completedCount / $totalVisitors) * 100, 1) : 0.0;

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
    public function calculateAverageTimes(Collection $completedQueues): array
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
            $serviceCount > 0 ? round($totalServiceTime / $serviceCount, 1) : 0.0,
            $waitingCount > 0 ? round($totalWaitingTime / $waitingCount, 1) : 0.0,
        ];
    }

    /**
     * Hitung metrik per instansi/departemen.
     */
    public function calculatePerDepartment(Collection $queues): array
    {
        $perDepartment = [];

        foreach (Department::all() as $dept) {
            $deptQueues = $queues->filter(fn ($q) => $q->department_id === $dept->id);

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
    public function buildDailySeries(string $startDate, string $endDate, Collection $queues): array
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
                $qDate = $q->booking_date instanceof Carbon
                    ? $q->booking_date->toDateString()
                    : (string) $q->booking_date;

                if (str_contains($qDate, ' ')) {
                    $qDate = explode(' ', $qDate)[0];
                }

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

    /**
     * Ekspor riwayat antrean laporan ke Excel.
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
     */
    public function exportPdf(Report $report): mixed
    {
        $queues = Queue::whereDate('booking_date', '>=', $report->start_date)
            ->whereDate('booking_date', '<=', $report->end_date)
            ->where('status', 'Completed')
            ->with(['user', 'department'])
            ->orderBy('booking_date')
            ->orderBy('queue_number')
            ->get();

        $pdf = Pdf::loadView('super_admin.reports.pdf', compact('report', 'queues'));
        $filename = 'laporan-antrean-'.$report->start_date->toDateString().'-to-'.$report->end_date->toDateString().'.pdf';

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}
