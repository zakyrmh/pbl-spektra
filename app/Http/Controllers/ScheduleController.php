<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Toggle status is_open satu schedule (Buka/Tutup sesi).
     * POST /admin/schedules/{schedule}/toggle-status
     */
    public function toggleStatus(Request $request, Schedule $schedule): JsonResponse
    {
        $user = Auth::user();

        if ($schedule->service->department_id !== $user->departments_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke jadwal ini.',
            ], 403);
        }

        $schedule->is_open = ! $schedule->is_open;
        $schedule->save();

        ActivityLog::record(
            action: 'TOGGLE_SCHEDULE_STATUS',
            modelType: 'Schedule',
            modelId: $schedule->id,
            description: "Operator mengubah status kuota layanan '{$schedule->service->name}' sesi '{$schedule->session_name}' menjadi ".($schedule->is_open ? 'Buka' : 'Tutup').'.',
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'is_open' => $schedule->is_open,
            'message' => 'Status sesi berhasil diperbarui.',
        ]);
    }

    /**
     * Toggle status is_open seluruh schedule hari ini untuk instansi operator.
     * POST /admin/schedules/toggle-all
     */
    public function toggleAll(Request $request): JsonResponse
    {
        $request->validate([
            'is_open' => 'required|boolean',
        ]);

        $user = Auth::user();

        if (! $user->departments_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke instansi mana pun.',
            ], 403);
        }

        $isOpen = (bool) $request->input('is_open');
        $today = Carbon::today()->toDateString();

        Schedule::whereDate('date', $today)
            ->whereHas('service', fn ($q) => $q->where('department_id', $user->departments_id))
            ->update(['is_open' => $isOpen]);

        ActivityLog::record(
            action: 'TOGGLE_ALL_SCHEDULES',
            modelType: 'Department',
            modelId: $user->departments_id,
            description: 'Operator mengubah status operasional seluruh sesi gerai hari ini menjadi '.($isOpen ? 'Buka' : 'Tutup').'.',
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'is_open' => $isOpen,
            'message' => 'Status operasional gerai berhasil diperbarui.',
        ]);
    }
}
