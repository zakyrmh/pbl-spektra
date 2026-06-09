<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminGerai;

use App\Events\QueueCreated;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DaftarTungguController extends Controller
{
    /**
     * Tampilkan daftar tunggu instansi admin gerai.
     * GET /admin/daftar-tunggu
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $department = Department::findOrFail($user->departments_id);

        // Ringkasan kuota hari ini
        $schedules = Schedule::whereDate('date', Carbon::today())
            ->whereHas('service', fn ($query) => $query->where('department_id', $department->id))
            ->with('service')
            ->get();

        // Layanan untuk filter
        $services = Service::where('department_id', $department->id)->get();

        // Build query bookings hari ini
        $bookingsQuery = Booking::where('booking_date', Carbon::today())
            ->whereHas('service', fn ($query) => $query->where('department_id', $department->id))
            ->with(['user', 'service', 'schedule']);

        // Filter: jenis layanan
        if ($request->filled('service_id')) {
            $bookingsQuery->where('service_id', $request->input('service_id'));
        }

        // Filter: search (booking_code atau nama warga)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $bookingsQuery->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $allBookings = $bookingsQuery->get();

        $pendingBookings = $allBookings->where('status', 'Pending');
        $checkedInBookings = $allBookings->where('status', 'Checked-In');
        $cancelledBookings = $allBookings->where('status', 'Cancelled');

        return view('admin.daftar-tunggu', compact(
            'department',
            'schedules',
            'services',
            'pendingBookings',
            'checkedInBookings',
            'cancelledBookings'
        ));
    }

    /**
     * Check-in manual booking dari daftar tunggu.
     * POST /admin/daftar-tunggu/{booking}/check-in
     */
    public function checkIn(Booking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->service->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        if (! $booking->canBeCheckedIn()) {
            return back()->with('error', 'Booking ini tidak dapat diproses check-in.');
        }

        $today = now()->toDateString();

        try {
            $queue = DB::transaction(function () use ($booking, $today) {
                $booking->update([
                    'status' => 'Checked-In',
                    'checked_in_at' => now(),
                ]);

                $counter = Counter::where('department_id', $booking->service->department_id)->first();
                if (! $counter) {
                    throw new \Exception('Belum ada loket/counter yang terdaftar untuk instansi '.$booking->service->department->name.'.');
                }

                $existingCount = Queue::where('counter_id', $counter->id)
                    ->whereDate('queue_date', $today)
                    ->lockForUpdate()
                    ->count();

                $nextNumber = $existingCount + 1;
                $prefix = $counter->department->inisial ?: 'Q';
                $queueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

                $queue = Queue::create([
                    'booking_id' => $booking->id,
                    'visitor_id' => null,
                    'counter_id' => $counter->id,
                    'service_id' => $booking->service_id,
                    'queue_number' => $queueNumber,
                    'status' => 'Waiting',
                    'queue_date' => $today,
                ]);

                ActivityLog::record(
                    action: 'VERIFY_CHECKIN',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Operator Gerai berhasil menyetujui check-in manual booking {$booking->booking_code} atas nama {$booking->user->name}. Nomor antrean {$queueNumber} diterbitkan.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

            event(new QueueCreated($queue));

            return back()->with('success', "Check-in manual berhasil! Warga {$booking->user->name} dengan antrean {$queue->queue_number} telah masuk daftar tunggu.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses check-in: '.$e->getMessage());
        }
    }

    /**
     * Pulihkan booking yang dibatalkan.
     * POST /admin/daftar-tunggu/{booking}/restore
     */
    public function restore(Booking $booking): RedirectResponse
    {
        $user = Auth::user();

        if ($booking->service->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        if ($booking->status !== 'Cancelled') {
            return back()->with('error', 'Hanya booking berstatus Cancelled yang dapat dipulihkan.');
        }

        try {
            DB::transaction(function () use ($booking) {
                $queue = Queue::where('booking_id', $booking->id)->first();

                if ($booking->checked_in_at && $queue) {
                    $booking->update(['status' => 'Checked-In', 'cancel_reason' => null]);
                    $queue->update(['status' => 'Waiting']);
                } else {
                    $booking->update(['status' => 'Pending', 'checked_in_at' => null, 'cancel_reason' => null]);
                }

                ActivityLog::record(
                    action: 'RESTORE_BOOKING',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Operator Gerai memulihkan status antrean booking {$booking->booking_code} milik {$booking->user->name}.",
                    actorUserId: Auth::id(),
                );
            });

            return back()->with('success', "Status booking {$booking->booking_code} berhasil dipulihkan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulihkan booking: '.$e->getMessage());
        }
    }
}
