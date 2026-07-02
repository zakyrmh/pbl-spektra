<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exports\QueuesExport;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Tentukan jumlah percobaan maksimal untuk job ini.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Tentukan jumlah detik sebelum job dianggap timeout.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Report $report,
        protected User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filename = 'exports/rekap-kunjungan-mpp-'.$this->report->start_date->toDateString().'-to-'.$this->report->end_date->toDateString().'-'.time().'.xlsx';

        // Simpan file excel ke storage publik menggunakan Maatwebsite Excel
        Excel::store(
            new QueuesExport($this->report->start_date->toDateString(), $this->report->end_date->toDateString()),
            $filename,
            'public'
        );

        // Dapatkan public URL dari file yang disimpan
        $downloadUrl = Storage::url($filename);

        // Buat notifikasi di database untuk User / Super Admin yang bersangkutan
        Notification::create([
            'user_id' => $this->user->id,
            'title' => 'Ekspor Excel Selesai',
            'message' => 'Laporan Rekap Kunjungan dari '.$this->report->start_date->toDateString().' hingga '.$this->report->end_date->toDateString().' telah selesai diekspor. Anda dapat mengunduhnya sekarang.',
            'data' => [
                'title' => 'Ekspor Excel Selesai',
                'message' => 'Laporan Rekap Kunjungan telah selesai diekspor.',
                'download_url' => $downloadUrl,
                'filename' => basename($filename),
            ],
        ]);
    }
}
