<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Queue as QueueModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard Pengunjung.
     */
    public function index(): View
    {
        $activeBooking = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->with(['service.department', 'queue'])
            ->latest()
            ->first();

        $currentServingQueue = 'Belum Mulai';
        $remainingQueuesCount = 0;
        $estimatedTime = 0;

        if ($activeBooking && $activeBooking->queue) {
            $queue = $activeBooking->queue;

            $currentServing = QueueModel::where('counter_id', $queue->counter_id)
                ->whereDate('queue_date', now()->toDateString())
                ->where('status', 'Serving')
                ->first();

            if ($currentServing) {
                $currentServingQueue = $currentServing->queue_number;
            }

            $remainingQueuesCount = QueueModel::where('counter_id', $queue->counter_id)
                ->whereDate('queue_date', now()->toDateString())
                ->where('status', 'Waiting')
                ->where('id', '<', $queue->id)
                ->count();

            $estimatedTime = $remainingQueuesCount * 3;
        }

        return view('dashboard.dashboard', [
            'activeBooking' => $activeBooking,
            'currentServingQueue' => $currentServingQueue,
            'remainingQueuesCount' => $remainingQueuesCount,
            'estimatedTime' => $estimatedTime,
        ]);
    }
}
