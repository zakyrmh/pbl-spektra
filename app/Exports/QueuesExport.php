<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Queue;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class QueuesExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    public function __construct(
        protected string $startDate,
        protected string $endDate
    ) {}

    /**
     * Query completed queues within the date range.
     */
    public function query()
    {
        return Queue::query()
            ->whereDate('queue_date', '>=', $this->startDate)
            ->whereDate('queue_date', '<=', $this->endDate)
            ->where('status', 'Completed')
            ->with(['booking.user', 'visitor', 'service.department'])
            ->orderBy('queue_date')
            ->orderBy('queue_number');
    }

    /**
     * Map data for each row.
     */
    public function map($queue): array
    {
        // Jika antrean walk-in, ambil dari visitor. Jika booking online, ambil dari booking->user.
        $name = $queue->visitor ? $queue->visitor->name : ($queue->booking?->user?->name ?? '-');
        $nik = $queue->visitor ? $queue->visitor->nik : ($queue->booking?->user?->nik ?? '-');
        $departmentName = $queue->service?->department?->name ?? '-';

        return [
            $queue->queue_date instanceof Carbon ? $queue->queue_date->toDateString() : (string) $queue->queue_date,
            $queue->queue_number,
            $nik,
            $name,
            $departmentName,
            $queue->service?->name ?? '-',
            $queue->called_at ? (Carbon::parse($queue->called_at)->format('H:i:s')) : '—',
            $queue->completed_at ? (Carbon::parse($queue->completed_at)->format('H:i:s')) : '—',
        ];
    }

    /**
     * Headings for the sheet.
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Nomor Antrean',
            'NIK',
            'Nama Lengkap',
            'Instansi/Gerai',
            'Layanan',
            'Jam Dipanggil',
            'Jam Selesai',
        ];
    }

    /**
     * Title of the sheet.
     */
    public function title(): string
    {
        return 'Rekap Kunjungan';
    }
}
