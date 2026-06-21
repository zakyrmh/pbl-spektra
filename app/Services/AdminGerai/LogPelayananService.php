<?php

declare(strict_types=1);

namespace App\Services\AdminGerai;

use App\Data\AdminGerai\LogPelayananData;
use App\Enums\QueueStatus;
use App\Models\Department;
use App\Models\Queue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Encapsulates all data-fetching logic for the Log Pelayanan page.
 *
 * Only reads from the queues table — never writes.
 */
final class LogPelayananService
{
    private const FINISHED_STATUSES = [
        QueueStatus::Completed->value,
        QueueStatus::Skipped->value,
    ];

    /**
     * Build a paginated query of finished queues for a given department,
     * applying optional date-range, status, and keyword filters.
     *
     * @param  array{start_date?:string, end_date?:string, status?:string, search?:string}  $filters
     */
    public function getPaginatedLogs(
        Department $department,
        array $filters = [],
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = Queue::where('department_id', $department->id)
            ->with(['user'])
            ->whereIn('status', $this->resolveStatuses($filters['status'] ?? null))
            ->orderBy('booking_date', 'desc')
            ->orderBy('completed_at', 'desc');

        if (! empty($filters['start_date'])) {
            $query->whereDate('booking_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('booking_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('queue_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Fetch all finished queues for CSV export (no pagination).
     *
     * @param  array{start_date?:string, end_date?:string, status?:string, search?:string}  $filters
     * @return Collection<int, Queue>
     */
    public function getAllForExport(Department $department, array $filters = []): Collection
    {
        $query = Queue::where('department_id', $department->id)
            ->with(['user'])
            ->whereIn('status', $this->resolveStatuses($filters['status'] ?? null))
            ->orderBy('booking_date', 'desc')
            ->orderBy('completed_at', 'desc');

        if (! empty($filters['start_date'])) {
            $query->whereDate('booking_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('booking_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('queue_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->get();
    }

    /**
     * Count completed queues for a department (for the summary card).
     */
    public function countByStatus(Department $department, string $status): int
    {
        return Queue::where('department_id', $department->id)
            ->where('status', $status)
            ->count();
    }

    /**
     * Get the streamed CSV callback for export.
     */
    public function getCsvExportCallback(Department $department, array $filters): \Closure
    {
        $queues = $this->getAllForExport($department, $filters);

        return function () use ($queues) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Tanggal',
                'Nomor Antrean',
                'Kode Booking',
                'Nama Warga',
                'Keperluan',
                'Jam Dipanggil',
                'Jam Selesai',
                'Durasi Pelayanan',
                'Status',
                'Catatan / Alasan',
            ]);

            foreach ($queues as $queue) {
                $data = LogPelayananData::fromModel($queue);
                fputcsv($file, [
                    $data->booking_date_formatted,
                    $data->queue_number,
                    $data->booking_code,
                    $data->visitor_name ?? '-',
                    $data->purpose ?? '-',
                    $data->called_at_formatted ?? '-',
                    $data->completed_at_formatted ?? '-',
                    $data->duration_label ?? '-',
                    $data->status,
                    $data->cancel_reason ?? '-',
                ]);
            }

            fclose($file);
        };
    }

    // ─────────────────────────────────────────────────────────────────────

    /**
     * Resolve which statuses to filter on.
     *
     * @return string[]
     */
    private function resolveStatuses(?string $status): array
    {
        if ($status && in_array($status, self::FINISHED_STATUSES, true)) {
            return [$status];
        }

        return self::FINISHED_STATUSES;
    }
}
