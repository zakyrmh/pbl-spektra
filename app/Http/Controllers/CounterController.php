<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\QueueCalled;
use App\Events\QueueCreated;
use App\Events\QueueFinished;
use App\Mail\FeedbackRequestMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CounterController extends Controller
{
    /**
     * Tampilkan halaman utama operator loket gerai.
     * GET /antrean
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Memeriksa penugasan loket
        if (! $user->counter_id) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $counter = Counter::with('department')->find($user->counter_id);
        if (! $counter) {
            return view('dashboard.dashboard', ['noCounter' => true]);
        }

        $today = Carbon::today();

        // 1. Antrean yang sedang aktif dilayani (Serving)
        $activeQueue = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Serving')
            ->with(['booking.user', 'visitor', 'service'])
            ->first();

        // 2. Daftar antrean menunggu (Waiting)
        $waitingQueues = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Waiting')
            ->with(['booking.user', 'visitor', 'service'])
            ->orderBy('id', 'asc')
            ->get();

        // 3. Daftar antrean terlewat (Skipped)
        $skippedQueues = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Skipped')
            ->with(['booking.user', 'visitor', 'service'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. Hitung Metrik Pelayanan Hari Ini
        $remainingCount = $waitingQueues->count();

        // Rata-rata durasi pelayanan (dalam menit)
        $completedToday = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Completed')
            ->whereNotNull('called_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($completedToday->isEmpty()) {
            $avgServiceTime = 12; // Nilai default estimasi (12 menit)
        } else {
            $totalSeconds = $completedToday->sum(fn (Queue $q) => $q->calculateDuration());
            $avgServiceTime = (int) round(($totalSeconds / $completedToday->count()) / 60);
            if ($avgServiceTime < 1) {
                $avgServiceTime = 1;
            }
        }

        return view('dashboard.dashboard', compact(
            'counter',
            'activeQueue',
            'waitingQueues',
            'skippedQueues',
            'remainingCount',
            'avgServiceTime'
        ));
    }

    /**
     * Update status operasional loket.
     * POST /api/counter/status
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:aktif,nonaktif,istirahat'],
        ]);

        $user = Auth::user();
        if (! $user->counter_id) {
            return response()->json(['message' => 'Anda belum ditugaskan ke loket mana pun.'], 403);
        }

        $counter = Counter::findOrFail($user->counter_id);
        $oldStatus = $counter->status;
        $counter->update(['status' => $request->input('status')]);

        // Catat di ActivityLog
        ActivityLog::record(
            action: 'UPDATE_COUNTER_STATUS',
            modelType: 'Counter',
            modelId: $counter->id,
            description: "Operator mengubah status loket '{$counter->name}' dari '{$oldStatus}' menjadi '{$counter->status}'.",
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'status' => $counter->status,
        ]);
    }

    /**
     * Memanggil antrean berikutnya secara otomatis (Next Queue).
     * POST /api/queues/call-next
     */
    public function callNext(): JsonResponse
    {
        $user = Auth::user();
        if (! $user->counter_id) {
            return response()->json(['message' => 'Anda belum ditugaskan ke loket mana pun.'], 403);
        }

        $today = Carbon::today();

        // Cari antrean waiting berikutnya
        $nextQueue = Queue::where('counter_id', $user->counter_id)
            ->whereDate('queue_date', $today)
            ->where('status', 'Waiting')
            ->orderBy('id', 'asc')
            ->first();

        if (! $nextQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrean berikutnya yang sedang menunggu.',
            ]);
        }

        return $this->callQueueDirect($nextQueue);
    }

    /**
     * Memanggil antrean tertentu berdasarkan ID (termasuk panggil ulang/panggil balik skipped).
     * POST /api/queues/{queue}/call
     */
    public function callQueue(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        return $this->callQueueDirect($queue);
    }

    /**
     * Menyelesaikan pelayanan antrean.
     * POST /api/queues/{queue}/finish
     */
    public function finishService(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        if ($queue->status !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean berstatus Serving yang dapat diselesaikan.',
            ], 422);
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

            // Kirim notifikasi sistem ke pengguna (jika booking online)
            if ($queue->booking && $queue->booking->user_id) {
                Notification::create([
                    'user_id' => $queue->booking->user_id,
                    'title' => 'Pelayanan Selesai',
                    'message' => "Pelayanan untuk nomor antrean {$queue->queue_number} telah selesai. Silakan isi ulasan dan berikan feedback Anda di menu Dashboard.",
                ]);
            }

            // Catat log
            ActivityLog::record(
                action: 'COMPLETE_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator menyelesaikan pelayanan untuk antrean {$queue->queue_number}.",
                actorUserId: Auth::id()
            );
        });

        // Trigger Event
        event(new QueueFinished($queue));

        // Kirim email notifikasi secara asinkron/aman (BR-11)
        if ($queue->booking && $queue->booking->user && $queue->booking->user->email) {
            try {
                Mail::to($queue->booking->user->email)->send(new FeedbackRequestMail($queue));
            } catch (\Throwable $e) {
                Log::error("SMTP Error: Gagal mengirim email notifikasi ulasan ke {$queue->booking->user->email}. Detil: ".$e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status antrean berhasil diperbarui.',
        ]);
    }

    /**
     * Melewatkan antrean aktif (Skip).
     * POST /api/queues/{queue}/skip
     */
    public function skipQueue(Queue $queue): JsonResponse
    {
        $this->authorize('manage', $queue);

        if ($queue->status !== 'Serving') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya antrean aktif yang sedang dilayani yang dapat dilewati.',
            ], 422);
        }

        DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'Skipped',
                'completed_at' => now(), // Catat waktu dilewati
            ]);

            // Catat log
            ActivityLog::record(
                action: 'SKIP_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator melewatkan nomor antrean {$queue->queue_number}.",
                actorUserId: Auth::id()
            );
        });

        // Trigger Event
        event(new QueueFinished($queue));

        return response()->json([
            'success' => true,
            'message' => 'Antrean berhasil dilewati.',
        ]);
    }

    /**
     * Memproses logika pemanggilan antrean (Internal Helper).
     */
    protected function callQueueDirect(Queue $queue): JsonResponse
    {
        $today = Carbon::today();

        $updatedQueue = DB::transaction(function () use ($queue, $today) {
            // 1. Selesaikan antrean lain di loket yang masih Serving (jika ada) demi mencegah stuck
            Queue::where('counter_id', $queue->counter_id)
                ->whereDate('queue_date', $today)
                ->where('status', 'Serving')
                ->where('id', '!=', $queue->id)
                ->update([
                    'status' => 'Completed',
                    'completed_at' => now(),
                ]);

            // 2. Update status antrean target ke Serving
            $queue->update([
                'status' => 'Serving',
                'called_at' => now(),
            ]);

            // 3. Catat di ActivityLog
            ActivityLog::record(
                action: 'CALL_QUEUE',
                modelType: 'Queue',
                modelId: $queue->id,
                description: "Operator memanggil nomor antrean {$queue->queue_number} ke loket.",
                actorUserId: Auth::id()
            );

            return $queue;
        });

        // 4. Broadcast event pemanggilan
        $loadedQueue = $updatedQueue->load(['counter.department', 'visitor', 'booking.user', 'service']);
        event(new QueueCalled($loadedQueue));

        return response()->json([
            'success' => true,
            'queue' => [
                'id' => $loadedQueue->id,
                'queue_number' => $loadedQueue->queue_number,
                'status' => $loadedQueue->status,
                'called_at' => $loadedQueue->called_at ? $loadedQueue->called_at->toIso8601String() : null,
                'visitor' => [
                    'name' => $loadedQueue->visitor ? $loadedQueue->visitor->name : ($loadedQueue->booking && $loadedQueue->booking->user ? $loadedQueue->booking->user->name : 'Warga'),
                    'nik' => $loadedQueue->visitor ? $loadedQueue->visitor->nik : ($loadedQueue->booking && $loadedQueue->booking->user ? $loadedQueue->booking->user->nik : '-'),
                ],
                'service' => [
                    'name' => $loadedQueue->service ? $loadedQueue->service->name : 'Layanan Umum',
                ],
            ],
        ]);
    }

    /**
     * Toggle status operasional instansi (buka/tutup) untuk instansi petugas yang bersangkutan.
     * POST /api/department/toggle-status
     */
    public function toggleDepartmentStatus(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            return response()->json(['message' => 'Anda tidak ditugaskan ke instansi mana pun.'], 403);
        }

        $department = Department::findOrFail($user->departments_id);
        $oldStatus = $department->is_open;
        $department->is_open = ! $oldStatus;
        $department->save();

        // Catat di ActivityLog
        ActivityLog::record(
            action: 'TOGGLE_DEPARTMENT_STATUS',
            modelType: 'Department',
            modelId: $department->id,
            description: "Operator mengubah status operasional instansi '{$department->name}' dari ".($oldStatus ? "'Buka'" : "'Tutup'").' menjadi '.($department->is_open ? "'Buka'" : "'Tutup'").'.',
            actorUserId: $user->id
        );

        return response()->json([
            'success' => true,
            'is_open' => $department->is_open,
            'message' => 'Status operasional instansi berhasil diperbarui.',
        ]);
    }

    /**
     * Tampilkan papan panggil khusus admin instansi.
     */
    public function papanPanggil(Request $request): View
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $department = Department::findOrFail($user->departments_id);
        $counter = Counter::where('department_id', $department->id)->first();

        // Ambil active booking dari session
        $activeBooking = null;
        $activeBookingId = session('papan_panggil_active_booking_id');
        if ($activeBookingId) {
            $activeBooking = Booking::with('user')->find($activeBookingId);
            // Bersihkan jika status booking sudah bukan Pending / Checked-In
            if ($activeBooking && ! in_array($activeBooking->status, ['Pending', 'Checked-In'])) {
                session()->forget('papan_panggil_active_booking_id');
                $activeBooking = null;
            }
        }

        // Live-feed sisa antrean hari ini yang berstatus Pending atau Checked-In
        $sisaBookings = Booking::where('booking_date', Carbon::today())
            ->whereHas('service', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->when($activeBooking, function ($query) use ($activeBooking) {
                $query->where('id', '!=', $activeBooking->id);
            })
            ->orderBy('id', 'asc')
            ->with('user')
            ->get();

        return view('admin.papan-panggil', compact(
            'department',
            'counter',
            'activeBooking',
            'sisaBookings'
        ));
    }

    /**
     * Panggil antrean berikutnya.
     */
    public function papanPanggilNext(Request $request)
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        // Cari antrean berikutnya untuk hari ini yang Pending/Checked-In
        $nextBooking = Booking::where('booking_date', Carbon::today())
            ->whereHas('service', function ($query) use ($user) {
                $query->where('department_id', $user->departments_id);
            })
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->orderBy('id', 'asc')
            ->first();

        if (! $nextBooking) {
            return back()->with('error', 'Tidak ada antrean tersisa untuk hari ini.');
        }

        // Jika statusnya masih Pending, otomatis ubah menjadi Checked-In saat dipanggil
        if ($nextBooking->status === 'Pending') {
            $nextBooking->update([
                'status' => 'Checked-In',
                'checked_in_at' => now(),
            ]);
        }

        session(['papan_panggil_active_booking_id' => $nextBooking->id]);

        return back()->with('success', 'Antrean '.$nextBooking->booking_code.' berhasil dipanggil.');
    }

    /**
     * Tandai antrean aktif sebagai selesai (Completed).
     */
    public function papanPanggilComplete(Booking $booking)
    {
        $user = Auth::user();
        // Pastikan booking ini berasal dari instansi operator
        if ($booking->service->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        $booking->update([
            'status' => 'Completed',
        ]);

        session()->forget('papan_panggil_active_booking_id');

        return back()->with('success', 'Antrean '.$booking->booking_code.' selesai dilayani.');
    }

    /**
     * Lewati/batalkan antrean aktif (Cancelled).
     */
    public function papanPanggilSkip(Request $request, Booking $booking)
    {
        $user = Auth::user();
        // Pastikan booking ini berasal dari instansi operator
        if ($booking->service->department_id !== $user->departments_id) {
            abort(403, 'Anda tidak berhak mengelola antrean instansi lain.');
        }

        $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'cancel_reason.required' => 'Alasan pembatalan/melewati antrean harus diisi.',
            'cancel_reason.min' => 'Alasan pembatalan harus minimal 5 karakter.',
        ]);

        $booking->update([
            'status' => 'Cancelled',
            'cancel_reason' => $request->input('cancel_reason'),
        ]);

        session()->forget('papan_panggil_active_booking_id');

        return back()->with('success', 'Antrean '.$booking->booking_code.' berhasil dilewati.');
    }

    /**
     * Tampilkan daftar tunggu gerai instansi (admin gerai).
     */
    public function daftarTunggu(Request $request): View
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $department = Department::findOrFail($user->departments_id);

        // 1. Ringkasan kuota hari ini
        $schedules = Schedule::whereDate('date', Carbon::today())
            ->whereHas('service', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->with('service')
            ->get();

        // 2. Layanan untuk filter
        $services = Service::where('department_id', $department->id)->get();

        // 3. Eager load bookings hari ini
        $bookingsQuery = Booking::where('booking_date', Carbon::today())
            ->whereHas('service', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->with(['user', 'service', 'schedule']);

        // Filter berdasarkan jenis layanan
        if ($request->filled('service_id')) {
            $bookingsQuery->where('service_id', $request->input('service_id'));
        }

        // Filter berdasarkan search input (booking_code atau nama warga)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $bookingsQuery->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $allBookings = $bookingsQuery->get();

        // Bagi data bookings menjadi 3 grup berdasarkan status
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
     */
    public function daftarTungguCheckIn(Booking $booking)
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
                // UPDATE status booking
                $booking->update([
                    'status' => 'Checked-In',
                    'checked_in_at' => now(),
                ]);

                // Cari loket berdasarkan instansi/departemen layanan
                $counter = Counter::where('department_id', $booking->service->department_id)->first();
                if (! $counter) {
                    throw new \Exception('Belum ada loket/counter yang terdaftar untuk instansi '.$booking->service->department->name.'.');
                }

                // Ambil nomor urut antrean terakhir hari ini
                $existingCount = Queue::where('counter_id', $counter->id)
                    ->whereDate('queue_date', $today)
                    ->lockForUpdate()
                    ->count();

                $nextNumber = $existingCount + 1;
                $prefix = $counter->department->inisial ?: 'Q';
                $queueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

                // Buat data Antrean Baru
                $queue = Queue::create([
                    'booking_id' => $booking->id,
                    'visitor_id' => null,
                    'counter_id' => $counter->id,
                    'service_id' => $booking->service_id,
                    'queue_number' => $queueNumber,
                    'status' => 'Waiting',
                    'queue_date' => $today,
                ]);

                // Catat di activity log
                ActivityLog::record(
                    action: 'VERIFY_CHECKIN',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Operator Gerai berhasil menyetujui check-in manual booking {$booking->booking_code} atas nama {$booking->user->name}. Nomor antrean {$queueNumber} diterbitkan.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

            // Broadcast event QueueCreated
            event(new QueueCreated($queue));

            return back()->with('success', "Check-in manual berhasil! Warga {$booking->user->name} dengan antrean {$queue->queue_number} telah masuk daftar tunggu.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses check-in: '.$e->getMessage());
        }
    }

    /**
     * Kembalikan status booking yang dibatalkan/dilewati.
     */
    public function daftarTungguRestore(Booking $booking)
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
                    $booking->update([
                        'status' => 'Checked-In',
                        'cancel_reason' => null,
                    ]);
                    $queue->update([
                        'status' => 'Waiting',
                    ]);
                } else {
                    $booking->update([
                        'status' => 'Pending',
                        'checked_in_at' => null,
                        'cancel_reason' => null,
                    ]);
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
