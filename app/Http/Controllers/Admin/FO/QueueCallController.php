<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueCallController extends Controller
{
    /**
     * Tampilkan halaman panggilan antrean FO.
     * GET /fo/call
     */
    public function index()
    {
        $today = Carbon::today();

        // Dapatkan loket front office
        $foDept = Department::where('inisial', 'FO')->first();
        if (! $foDept) {
            abort(404, 'Department Front Office (FO) tidak ditemukan.');
        }

        $counter = Counter::where('department_id', $foDept->id)->first();
        if (! $counter) {
            abort(404, 'Loket Front Office tidak ditemukan.');
        }

        // Antrean yang sedang dilayani (Serving)
        $currentQueue = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Serving')
            ->first();

        // Antrean berikutnya yang sedang menunggu (Waiting)
        $nextQueue = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Waiting')
            ->orderBy('id', 'asc')
            ->first();

        // Riwayat antrean hari ini
        $historyQueues = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->with(['visitor', 'booking.user', 'service', 'feedback'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Hitung total statistik hari ini
        $totalWaiting = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Waiting')
            ->count();

        $totalServed = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Completed')
            ->count();

        $totalSkipped = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Skipped')
            ->count();

        return view('admin.fo.call', compact(
            'currentQueue',
            'nextQueue',
            'historyQueues',
            'totalWaiting',
            'totalServed',
            'totalSkipped',
            'counter'
        ));
    }

    /**
     * Panggil antrean berikutnya.
     * POST /fo/call/next
     */
    public function next()
    {
        $today = Carbon::today();
        $foDept = Department::where('inisial', 'FO')->first();
        $counter = Counter::where('department_id', $foDept->id)->first();

        $nextQueue = null;

        DB::transaction(function () use ($counter, $today, &$nextQueue) {
            // Selesaikan antrean yang sedang dilayani saat ini (Serving -> Completed)
            Queue::where('counter_id', $counter->id)
                ->where('queue_date', $today)
                ->where('status', 'Serving')
                ->update([
                    'status' => 'Completed',
                    'completed_at' => now(),
                ]);

            // Panggil antrean berikutnya (Waiting -> Serving)
            $nextQueue = Queue::where('counter_id', $counter->id)
                ->where('queue_date', $today)
                ->where('status', 'Waiting')
                ->orderBy('id', 'asc')
                ->first();

            if ($nextQueue) {
                $nextQueue->update([
                    'status' => 'Serving',
                    'called_at' => now(),
                ]);

                ActivityLog::record(
                    action: 'CALL_NEXT_FO',
                    modelType: 'Queue',
                    modelId: $nextQueue->id,
                    description: "Memanggil antrean FO berikutnya: {$nextQueue->queue_number}",
                    actorUserId: Auth::id()
                );
            }
        });

        if ($nextQueue) {
            return redirect()->route('admin.fo.call')
                ->with('success', "Nomor antrean <strong>{$nextQueue->queue_number}</strong> berhasil dipanggil.")
                ->with('play_chime', true);
        }

        return redirect()->route('admin.fo.call')
            ->with('warning', 'Tidak ada antrean berikutnya yang sedang menunggu.');
    }

    /**
     * Panggil ulang antrean yang sedang dilayani.
     * POST /fo/call/recall
     */
    public function recall()
    {
        $today = Carbon::today();
        $foDept = Department::where('inisial', 'FO')->first();
        $counter = Counter::where('department_id', $foDept->id)->first();

        $currentQueue = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Serving')
            ->first();

        if ($currentQueue) {
            $currentQueue->update([
                'called_at' => now(),
            ]);

            ActivityLog::record(
                action: 'RECALL_FO',
                modelType: 'Queue',
                modelId: $currentQueue->id,
                description: "Memanggil ulang antrean FO: {$currentQueue->queue_number}",
                actorUserId: Auth::id()
            );

            return redirect()->route('admin.fo.call')
                ->with('success', "Memanggil ulang nomor antrean <strong>{$currentQueue->queue_number}</strong>.")
                ->with('play_chime', true);
        }

        return redirect()->route('admin.fo.call')
            ->with('error', 'Tidak ada antrean yang sedang aktif dilayani untuk dipanggil ulang.');
    }

    /**
     * Lewati antrean saat ini.
     * POST /fo/call/skip
     */
    public function skip()
    {
        $today = Carbon::today();
        $foDept = Department::where('inisial', 'FO')->first();
        $counter = Counter::where('department_id', $foDept->id)->first();

        $currentQueue = Queue::where('counter_id', $counter->id)
            ->where('queue_date', $today)
            ->where('status', 'Serving')
            ->first();

        if ($currentQueue) {
            $currentQueue->update([
                'status' => 'Skipped',
                'completed_at' => now(),
            ]);

            ActivityLog::record(
                action: 'SKIP_FO',
                modelType: 'Queue',
                modelId: $currentQueue->id,
                description: "Melewatkan antrean FO: {$currentQueue->queue_number}",
                actorUserId: Auth::id()
            );

            return redirect()->route('admin.fo.call')
                ->with('success', "Nomor antrean <strong>{$currentQueue->queue_number}</strong> dilewati.");
        }

        return redirect()->route('admin.fo.call')
            ->with('error', 'Tidak ada antrean yang sedang aktif dilayani untuk dilewati.');
    }
}
