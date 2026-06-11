<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Department;
use App\Models\Queue as QueueModel;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminFoDashboardController extends Controller
{
    /**
     * Tampilkan dashboard Admin Front Office.
     */
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        $departments = Department::with('counters')->get();

        $recentQueues = QueueModel::query()
            ->whereDate('queue_date', $today)
            ->with(['booking.user', 'visitor', 'service.department', 'counter.department'])
            ->latest()
            ->take(8)
            ->get();

        $todayFoQueueCount = Booking::query()
            ->whereDate('booking_date', $today)
            ->where('status', 'Pending')
            ->count('*');

        $todayTotalPrintedTickets = QueueModel::query()
            ->whereDate('queue_date', $today)
            ->count('*');

        return view('dashboard.dashboard', compact(
            'departments',
            'recentQueues',
            'todayFoQueueCount',
            'todayTotalPrintedTickets'
        ));
    }
}
