<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\CallNextQueueRequest;
use App\Models\Queue;
use App\Services\DashboardAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DashboardController — Role-based dispatcher untuk MPP Kota Sawahlunto.
 */
class DashboardController extends Controller
{
    protected DashboardAnalyticsService $analyticsService;

    /**
     * DashboardController constructor.
     */
    public function __construct(DashboardAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Tampilkan halaman dashboard utama berdasarkan role pengguna.
     */
    public function index(Request $request): mixed
    {
        $user = Auth::user();
        $role = $user->role;

        // Resolving BackedEnum or string representation of UserRole
        $roleValue = $role instanceof \BackedEnum ? $role : UserRole::tryFrom((string) $role);

        // Menyelaraskan role warga ke pengunjung
        if ($roleValue === null || $role === 'warga') {
            $roleValue = UserRole::Pengunjung;
        }

        $data = [];
        $today = Carbon::today()->toDateString();

        if ($roleValue === UserRole::SuperAdmin) {
            $dashboardData = $this->analyticsService->getSuperAdminDashboardData($today);
            $data = $dashboardData->toArray();
        } elseif ($roleValue === UserRole::AdminFo) {
            $dashboardData = $this->analyticsService->getFoDashboardData($today);
            $data = $dashboardData->toArray();
        } elseif ($roleValue === UserRole::AdminGerai) {
            $department = $user->department;
            if (! $department) {
                $data = ['noCounter' => true];
            } else {
                $currentQueue = Queue::where('department_id', $department->id)
                    ->whereDate('booking_date', $today)
                    ->where('status', 'Serving')
                    ->with('user')
                    ->first();

                $waitingQueues = Queue::where('department_id', $department->id)
                    ->whereDate('booking_date', $today)
                    ->where('status', 'Checked-In')
                    ->with('user')
                    ->orderBy('id', 'asc')
                    ->get();

                $skippedQueues = Queue::where('department_id', $department->id)
                    ->whereDate('booking_date', $today)
                    ->where('status', 'Skipped')
                    ->with('user')
                    ->orderBy('updated_at', 'desc')
                    ->get();

                $completedCount = Queue::where('department_id', $department->id)
                    ->whereDate('booking_date', $today)
                    ->where('status', 'Completed')
                    ->count();

                // Rata-rata durasi pelayanan (dalam menit)
                $completedToday = Queue::where('department_id', $department->id)
                    ->whereDate('booking_date', $today)
                    ->where('status', 'Completed')
                    ->whereNotNull('called_at')
                    ->whereNotNull('completed_at')
                    ->get();

                if ($completedToday->isEmpty()) {
                    $avgServiceTime = 12; // nilai default estimasi
                } else {
                    $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
                    $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
                    $avgServiceTime = max($avgServiceTime, 1);
                }

                $remainingCount = $waitingQueues->count();

                $data = [
                    'department' => $department,
                    'currentQueue' => $currentQueue,
                    'activeQueue' => $currentQueue, // fallback compatibility
                    'waitingQueues' => $waitingQueues,
                    'skippedQueues' => $skippedQueues,
                    'completedCount' => $completedCount,
                    'remainingCount' => $remainingCount,
                    'avgServiceTime' => $avgServiceTime,
                    'noCounter' => false,
                ];
            }
        } elseif ($roleValue === UserRole::Pengunjung) {
            $activeBooking = Queue::where('user_id', $user->id)
                ->whereIn('status', ['Booked', 'Checked-In', 'Serving'])
                ->with(['department'])
                ->latest()
                ->first();

            $currentServingQueue = 'Belum Mulai';
            $remainingQueuesCount = 0;
            $estimatedTime = 0;

            if ($activeBooking && $activeBooking->queue_number) {
                $currentServing = Queue::where('department_id', $activeBooking->department_id)
                    ->whereDate('booking_date', $activeBooking->booking_date)
                    ->where('status', 'Serving')
                    ->first();

                if ($currentServing) {
                    $currentServingQueue = $currentServing->queue_number;
                }

                $remainingQueuesCount = Queue::where('department_id', $activeBooking->department_id)
                    ->whereDate('booking_date', $activeBooking->booking_date)
                    ->where('status', 'Checked-In')
                    ->where('id', '<', $activeBooking->id)
                    ->count();

                // Hitung estimasi waktu tunggu
                // Rata-rata durasi pelayanan (dalam menit)
                $completedToday = Queue::where('department_id', $activeBooking->department_id)
                    ->whereDate('booking_date', $activeBooking->booking_date)
                    ->where('status', 'Completed')
                    ->whereNotNull('called_at')
                    ->whereNotNull('completed_at')
                    ->get();

                if ($completedToday->isEmpty()) {
                    $avgServiceTime = 12; // nilai default estimasi
                } else {
                    $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
                    $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
                    $avgServiceTime = max($avgServiceTime, 1);
                }

                $estimatedTime = $remainingQueuesCount * $avgServiceTime;
            }

            $data = [
                'activeBooking' => $activeBooking,
                'currentServingQueue' => $currentServingQueue,
                'remainingQueuesCount' => $remainingQueuesCount,
                'estimatedTime' => $estimatedTime,
            ];
        }

        return view('dashboard.dashboard', $data);
    }

    /**
     * Halaman manajemen antrean.
     */
    public function manageQueue()
    {
        return view('dashboard.dashboard');
    }

    /**
     * Panggil antrean berikutnya.
     */
    public function callNext(CallNextQueueRequest $request)
    {
        return back()->with('success', 'Antrean berikutnya telah dipanggil.');
    }
}
