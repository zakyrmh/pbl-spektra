<?php

declare(strict_types=1);

namespace App\Services\AdminGerai;

use App\Enums\QueueStatus;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * BoothOperationService
 *
 * Encapsulates all queue-state transitions for an operator booth (Admin Gerai):
 *  - holdQueue    : Pause a Serving queue → Hold
 *  - forwardQueue : Re-assign a queue to another department (Checked-In)
 */
final class BoothOperationService
{
    /**
     * Place the currently Serving queue on Hold.
     * The queue is paused but not completed or skipped.
     *
     * @throws \RuntimeException if queue is not currently Serving.
     */
    public function holdQueue(Queue $queue): void
    {
        if ($queue->status !== QueueStatus::Serving->value) {
            throw new \RuntimeException('Hanya antrean yang sedang dilayani yang dapat ditunda.');
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => QueueStatus::Hold->value,
            ]);

            ActivityLog::record(
                action: 'HOLD_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menunda antrean {$queue->queue_number} ({$queue->booking_code}).",
                actorUserId: Auth::id()
            );
        });
    }

    /**
     * Forward (re-assign) a queue to a different department.
     * The queue status is reset to Checked-In so the target booth can call it next.
     *
     * @throws \RuntimeException if the queue is already Completed, Cancelled, or Skipped.
     */
    public function forwardQueue(Queue $queue, Department $targetDepartment): void
    {
        $terminalStatuses = QueueStatus::finished();
        if (in_array(QueueStatus::tryFrom($queue->status), $terminalStatuses, true)) {
            throw new \RuntimeException('Antrean yang sudah selesai tidak dapat diopersikan ke instansi lain.');
        }

        DB::transaction(function () use ($queue, $targetDepartment) {
            $originalDeptName = $queue->department?->name ?? 'Tidak diketahui';

            $queue->update([
                'department_id' => $targetDepartment->id,
                'status' => QueueStatus::CheckedIn->value,
                'called_at' => null,
            ]);

            ActivityLog::record(
                action: 'FORWARD_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator mengoper antrean {$queue->queue_number} dari instansi '{$originalDeptName}' ke instansi '{$targetDepartment->name}'.",
                actorUserId: Auth::id()
            );

            // Notify the citizen about the forwarding
            if ($queue->user) {
                $queue->user->notifications()->create([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\QueueForwarded',
                    'data' => [
                        'title' => 'Antrean Dipindahkan',
                        'message' => "Nomor antrean {$queue->queue_number} Anda telah dipindahkan ke instansi {$targetDepartment->name}. Silakan menunggu panggilan di loket baru.",
                    ],
                ]);
            }
        });
    }
}
