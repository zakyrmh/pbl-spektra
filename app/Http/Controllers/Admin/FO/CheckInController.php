<?php

namespace App\Http\Controllers\Admin\FO;

use App\Events\QueueCreated;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CheckInController — Verifikasi & Check-In Booking Online
 *
 * Rute   : admin.fo.checkin        → GET  /fo/check-in
 * Rute   : admin.fo.checkin.verify → POST /fo/check-in/verify
 *
 * Business Rules (AGENT.md §7):
 *  BR-02 Nomor antrian aktif hanya diterbitkan oleh front_office.
 *  REQ-2.1 FO dapat mencari booking berdasarkan NIK atau booking_code.
 */
class CheckInController extends Controller
{
    /**
     * Halaman utama check-in: tampilkan form pencarian kode booking.
     * GET /fo/check-in
     */
    public function index()
    {
        return view('admin.fo.checkin');
    }

    /**
     * Verifikasi kode booking & lakukan check-in.
     * POST /fo/check-in/verify
     *
     * Request fields:
     *  - booking_code : string — UUID atau kode pendek booking
     *  - nik_input    : string|nullable — NIK yang diisikan FO jika warga belum punya NIK
     */
    public function verify(Request $request)
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:36'],
            'nik_input' => ['nullable', 'string', 'digits:16'],
        ]);

        $code = trim($request->input('booking_code'));

        // ── 1. Cari booking berdasarkan booking_code ──────────────────
        $booking = Booking::where('booking_code', $code)
            ->with('user')
            ->first();

        if (! $booking) {
            return back()
                ->withInput()
                ->with('error', "Kode booking <strong>{$code}</strong> tidak ditemukan. Pastikan kode yang Anda masukkan sudah benar.")
                ->with('searched_code', $code);
        }

        // ── 2. Validasi status booking ────────────────────────────────
        if (! $booking->canBeCheckedIn()) {
            $statusLabel = match ($booking->status) {
                'Checked-In' => 'sudah di-check-in sebelumnya',
                'Completed' => 'sudah selesai dilayani',
                'Cancelled' => 'telah dibatalkan',
                default => "berstatus {$booking->status}",
            };

            return back()
                ->withInput()
                ->with('warning', "Booking ini tidak dapat diproses karena {$statusLabel}.")
                ->with('booking', $booking);
        }

        // ── 3. Cek NIK warga — jika kosong, kembalikan ke view dengan flag ─
        $user = $booking->user;

        if (empty($user->nik)) {
            // Jika Admin FO sudah mengisi NIK via inline form, simpan dulu
            if ($request->filled('nik_input')) {
                $nikBaru = $request->input('nik_input');

                // Pastikan NIK belum dipakai user lain
                $nikSudahAda = User::where('nik', $nikBaru)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($nikSudahAda) {
                    return back()
                        ->withInput()
                        ->with('error', "NIK <strong>{$nikBaru}</strong> sudah terdaftar di sistem untuk pengguna lain.")
                        ->with('booking', $booking)
                        ->with('nik_required', true);
                }

                $user->update(['nik' => $nikBaru]);

                ActivityLog::record(
                    action: 'UPDATE_NIK',
                    modelType: 'User',
                    modelId: $user->id,
                    description: "Admin FO ({$user->id}) mengisi NIK warga {$user->name} → {$nikBaru} saat proses check-in booking {$booking->booking_code}.",
                    actorUserId: Auth::id(),
                );
            } else {
                // NIK masih kosong & FO belum mengisi → kembalikan dengan flag untuk tampil form NIK
                return back()
                    ->withInput()
                    ->with('booking', $booking)
                    ->with('nik_required', true);
            }
        }

        // ── 4. Lakukan Check-In dalam satu transaksi ──────────────────
        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'Checked-In',
                'checked_in_at' => now(),
            ]);

            // Catat di activity_logs (AGENT.md §8 — Auditability)
            ActivityLog::record(
                action: 'VERIFY_CHECKIN',
                modelType: 'Booking',
                modelId: $booking->id,
                description: "Admin FO berhasil check-in booking {$booking->booking_code} atas nama {$booking->user->name}.",
                actorUserId: Auth::id(),
            );
        });

        return redirect()->route('admin.fo.checkin')
            ->with('success', "Check-in berhasil! Warga <strong>{$booking->user->name}</strong> ({$booking->booking_code}) telah tercatat hadir.")
            ->with('checkin_result', $booking->fresh(['user']));
    }

    /**
     * GET /api/fo/bookings/verify
     */
    public function verifyApi(Request $request)
    {
        $code = trim($request->query('code', ''));

        if (empty($code)) {
            return response()->json(['message' => 'Booking code is required.'], 400);
        }

        $booking = Booking::where('booking_code', $code)
            ->where('status', 'Pending')
            ->with(['user', 'service.department'])
            ->first();

        if (! $booking) {
            return response()->json(['message' => 'Booking not found or already verified.'], 404);
        }

        return response()->json([
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'user' => [
                'name' => $booking->user->name,
                'nik' => $booking->user->nik,
            ],
            'department' => [
                'name' => $booking->service->department->name,
            ],
            'service' => [
                'name' => $booking->service->name,
            ],
        ]);
    }

    /**
     * POST /api/fo/bookings/{booking}/checkin
     */
    public function checkInApi(Request $request, Booking $booking)
    {
        if ($booking->status !== 'Pending') {
            return response()->json(['message' => 'Booking status is not Pending.'], 422);
        }

        $today = now()->toDateString();

        try {
            $queue = DB::transaction(function () use ($booking, $today) {
                // UPDATE status booking
                $booking->update([
                    'status' => 'Checked-In',
                    'checked_in_at' => now(),
                ]);

                // Find a counter for the department
                $counter = Counter::where('department_id', $booking->service->department_id)->first();
                if (! $counter) {
                    throw new \Exception('No counter available for this department.');
                }

                // Calculate queue number
                $existingCount = Queue::where('counter_id', $counter->id)
                    ->whereDate('queue_date', $today)
                    ->lockForUpdate()
                    ->count();

                $nextNumber = $existingCount + 1;
                $prefix = $counter->department->inisial ?: 'Q';
                $queueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

                // INSERT new Queue
                $queue = Queue::create([
                    'booking_id' => $booking->id,
                    'visitor_id' => null,
                    'counter_id' => $counter->id,
                    'service_id' => $booking->service_id,
                    'queue_number' => $queueNumber,
                    'status' => 'Waiting',
                    'queue_date' => $today,
                ]);

                // Record activity log
                ActivityLog::record(
                    action: 'VERIFY_CHECKIN',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Admin FO berhasil check-in booking {$booking->booking_code} atas nama {$booking->user->name}.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

            // Broadcast the QueueCreated event
            event(new QueueCreated($queue));

            return response()->json([
                'success' => true,
                'queue_number' => $queue->queue_number,
                'status' => $queue->status,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * POST /api/fo/queues/walkin
     */
    public function walkInApi(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'service_name' => ['nullable', 'string'],
        ]);

        $nik = $request->input('nik');
        $name = $request->input('name');
        $purpose = $request->input('purpose');
        $departmentId = $request->input('department_id');
        $serviceName = $request->input('service_name');
        $today = now()->toDateString();

        try {
            $queue = DB::transaction(function () use ($nik, $name, $purpose, $departmentId, $serviceName, $today) {
                // Find service
                $service = null;
                if ($serviceName) {
                    $service = Service::where('department_id', $departmentId)
                        ->where('name', $serviceName)
                        ->first();
                }

                if (! $service) {
                    $service = Service::where('department_id', $departmentId)->first();
                }

                // Find counter
                $counter = Counter::where('department_id', $departmentId)->first();
                if (! $counter) {
                    throw new \Exception('No counter available for this department.');
                }

                // Get or Create visitor
                $visitor = Visitor::firstOrCreate(
                    ['nik' => $nik],
                    [
                        'name' => $name,
                        'phone' => '00000000000', // Default fallback since walk-in might not have phone
                        'purpose' => $purpose,
                    ]
                );

                // If the visitor already exists, we might want to update the purpose if needed
                // But typically firstOrCreate is enough. Let's just update the purpose to the latest
                if (! $visitor->wasRecentlyCreated) {
                    $visitor->update(['purpose' => $purpose, 'name' => $name]);
                }

                // Calculate queue number
                $existingCount = Queue::where('counter_id', $counter->id)
                    ->whereDate('queue_date', $today)
                    ->lockForUpdate()
                    ->count();

                $nextNumber = $existingCount + 1;
                $prefix = $counter->department->inisial ?: 'W';
                $queueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

                // INSERT new Queue
                $queue = Queue::create([
                    'booking_id' => null,
                    'visitor_id' => $visitor->id,
                    'counter_id' => $counter->id,
                    'service_id' => $service?->id,
                    'queue_number' => $queueNumber,
                    'status' => 'Waiting',
                    'queue_date' => $today,
                ]);

                // Record activity log
                ActivityLog::record(
                    action: 'WALKIN_TICKET',
                    modelType: 'Queue',
                    modelId: $queue->id,
                    description: "Admin FO mencetak tiket mandiri Walk-In ({$queueNumber}) tujuan {$counter->department->name} untuk {$visitor->name}.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

            // Broadcast the QueueCreated event
            event(new QueueCreated($queue));

            return response()->json([
                'success' => true,
                'queue_number' => $queue->queue_number,
                'status' => $queue->status,
                'visitor_name' => $queue->visitor->name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/fo/visitors/check-nik
     */
    public function checkNikApi(Request $request)
    {
        $nik = trim($request->query('nik', ''));

        if (empty($nik) || strlen($nik) !== 16) {
            return response()->json(['message' => 'Format NIK tidak valid.'], 400);
        }

        $visitor = Visitor::where('nik', $nik)->first();

        if ($visitor) {
            return response()->json([
                'found' => true,
                'name' => $visitor->name,
                'nik' => $visitor->nik,
            ]);
        }

        return response()->json(['found' => false]);
    }
}
