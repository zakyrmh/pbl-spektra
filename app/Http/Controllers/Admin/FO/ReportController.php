<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFO\StoreReportRequest;
use App\Models\Report;
use App\Services\AdminFO\ReportAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
final class ReportController extends Controller
{
    public function __construct(
        protected ReportAnalyticsService $analyticsService
    ) {}

    // =========================================================================
    // FRONT OFFICE METHODS
    // =========================================================================

    /**
     * Tampilkan daftar laporan untuk Admin Front Office.
     * GET /fo/reports
     */
    public function foIndex(): View
    {
        $reports = $this->analyticsService->getAllReports();

        return view('admin.fo.reports.index', compact('reports'));
    }

    /**
     * Tampilkan detail visualisasi analitik laporan untuk Admin FO.
     * GET /fo/reports/{report}
     */
    #[\NoDiscard]
    public function foShow(Report $report): View
    {
        $queues = $this->analyticsService->getCompletedQueuesForReport($report);

        return view('admin.fo.reports.show', compact('report', 'queues'));
    }

    /**
     * Simpan laporan baru yang digenerate oleh FO.
     * POST /fo/reports
     */
    public function foStore(StoreReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $queues = $this->analyticsService->getQueuesForRange($validated['start_date'], $validated['end_date']);

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada rentang tanggal tersebut.');
        }

        $this->analyticsService->createReport($validated, $queues);

        return redirect()->route('admin.fo.reports.index')
            ->with('success', 'Laporan berhasil dibuat.');
    }

    /**
     * Update/generate ulang laporan yang belum dikirim.
     * PUT /fo/reports/{report}
     */
    public function foUpdate(StoreReportRequest $request, Report $report): RedirectResponse
    {
        if ($report->status === 'Terkirim') {
            return redirect()->route('admin.fo.reports.index')
                ->with('error', 'Laporan yang telah dikirim ke Super Admin tidak dapat diubah.');
        }

        $validated = $request->validated();
        $queues = $this->analyticsService->getQueuesForRange($validated['start_date'], $validated['end_date']);

        if ($queues->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada data antrean pada rentang tanggal tersebut untuk diperbarui.');
        }

        $this->analyticsService->updateReport($report, $validated, $queues);

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
        $this->analyticsService->deleteReport($report);

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

        $this->analyticsService->sendReport($report);

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
        $reports = $this->analyticsService->getSentReports();

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

        $queues = $this->analyticsService->getCompletedQueuesForReport($report);

        return view('super_admin.reports.show', compact('report', 'queues'));
    }

    /**
     * Ekspor riwayat antrean laporan ke Excel.
     * GET /laporan-analitik/{report}/export/excel
     */
    public function exportExcel(Report $report): mixed
    {
        return $this->analyticsService->exportExcel($report);
    }

    /**
     * Ekspor riwayat antrean laporan ke PDF.
     * GET /laporan-analitik/{report}/export/pdf
     */
    public function exportPdf(Report $report): mixed
    {
        return $this->analyticsService->exportPdf($report);
    }
}
