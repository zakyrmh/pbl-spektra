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
            ->whereDate('booking_date', '>=', $this->startDate)
            ->whereDate('booking_date', '<=', $this->endDate)
            ->where('status', 'Completed')
            ->with(['user', 'department'])
            ->orderBy('booking_date')
            ->orderBy('queue_number');
    }

    /**
     * Map data for each row.
     */
    public function map($queue): array
    {
        $name = $queue->user?->name ?? '-';
        $nik = $queue->user?->nik ?? '-';
        $departmentName = $queue->department?->name ?? '-';

        $phone = $queue->user?->no_telp ?? '-';

        return [
            $queue->booking_date instanceof Carbon ? $queue->booking_date->toDateString() : (string) $queue->booking_date,
            $queue->queue_number,
            $nik,
            $name,
            $phone,
            $departmentName,
            $queue->purpose ?? '-',
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
            'Nomor HP Pengunjung',
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
