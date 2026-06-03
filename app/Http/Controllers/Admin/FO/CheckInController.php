<?php

namespace App\Http\Controllers\Admin\FO;

use App\Events\QueueCreated;
use App\Http\Controllers\Controller;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * CheckInController — Verifikasi & Check-In Booking Online
 *
 * Rute   : admin.fo.checkin        → GET  /fo/check-in
 * Rute   : admin.fo.checkin.verify → POST /fo/check-in/verify
 * Rute   : admin.fo.checkin.approve → POST /fo/check-in/{booking}/approve
 * Rute   : admin.fo.checkin.reject  → POST /fo/check-in/{booking}/reject
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
     * Verifikasi kode booking & tampilkan detail untuk persetujuan.
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
            ->with(['user', 'service.department', 'schedule'])
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
                    description: "Admin FO mengisi NIK warga {$user->name} → {$nikBaru} saat proses check-in booking {$booking->booking_code}.",
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

        // ── 4. Tampilkan halaman konfirmasi detail dokumen ────────────
        return view('admin.fo.checkin', [
            'booking' => $booking,
            'nik_required' => false,
        ]);
    }

    /**
     * Setujui verifikasi dokumen dan terbitkan nomor antrean.
     * POST /fo/check-in/{booking}/approve
     */
    public function approve(Request $request, Booking $booking)
    {
        // Pastikan booking bisa dicheck-in
        if (! $booking->canBeCheckedIn()) {
            return redirect()->route('admin.fo.checkin')
                ->with('warning', 'Booking ini tidak dapat diproses.');
        }

        // Pastikan NIK sudah diisi
        if (empty($booking->user->nik)) {
            return redirect()->route('admin.fo.checkin')
                ->with('booking', $booking)
                ->with('nik_required', true)
                ->with('error', 'NIK wajib diisi sebelum menyetujui check-in.');
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
                    description: "Admin FO berhasil menyetujui dokumen & check-in booking {$booking->booking_code} atas nama {$booking->user->name}. Nomor antrean {$queueNumber} diterbitkan.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

            // Broadcast event QueueCreated
            event(new QueueCreated($queue));

            return redirect()->route('admin.fo.checkin')
                ->with('success', "Check-in berhasil! Warga <strong>{$booking->user->name}</strong> telah terverifikasi.")
                ->with('checkin_result', $booking->fresh(['user', 'queue.counter.department', 'service']));
        } catch (\Exception $e) {
            return redirect()->route('admin.fo.checkin')
                ->withInput()
                ->with('error', 'Gagal memproses check-in: '.$e->getMessage());
        }
    }

    /**
     * Tolak verifikasi dokumen warga (batalkan booking).
     * POST /fo/check-in/{booking}/reject
     */
    public function reject(Request $request, Booking $booking)
    {
        // Pastikan booking bisa dicheck-in
        if (! $booking->canBeCheckedIn()) {
            return redirect()->route('admin.fo.checkin')
                ->with('warning', 'Booking ini tidak dapat diproses.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
            'reason.min' => 'Alasan penolakan minimal harus 5 karakter.',
            'reason.max' => 'Alasan penolakan maksimal 255 karakter.',
        ]);

        $reason = trim($request->input('reason'));

        try {
            DB::transaction(function () use ($booking, $reason) {
                // Update booking status
                $booking->update([
                    'status' => 'Cancelled',
                    'cancel_reason' => $reason,
                ]);

                // Buat notifikasi sistem
                Notification::create([
                    'user_id' => $booking->user_id,
                    'title' => 'Booking Ditolak FO',
                    'message' => "Reservasi antrean untuk layanan {$booking->service->name} pada {$booking->booking_date->translatedFormat('d F Y')} ditolak oleh petugas Front Office dengan alasan: {$reason}.",
                ]);

                // Catat di activity log
                ActivityLog::record(
                    action: 'REJECT_BOOKING',
                    modelType: 'Booking',
                    modelId: $booking->id,
                    description: "Admin FO menolak booking {$booking->booking_code} milik {$booking->user->name} karena dokumen fisik tidak lengkap/tidak sesuai. Alasan: {$reason}.",
                    actorUserId: Auth::id(),
                );
            });

            // Kirim email pembatalan ke customer
            try {
                Mail::to($booking->user->email)->send(new BookingCancelledMail($booking));
            } catch (\Exception $e) {
                Log::warning("REJECT_BOOKING: Gagal mengirim email ke {$booking->user->email} untuk booking {$booking->booking_code}: ".$e->getMessage());
            }

            return redirect()->route('admin.fo.checkin')
                ->with('success', "Booking <strong>{$booking->booking_code}</strong> atas nama <strong>{$booking->user->name}</strong> berhasil ditolak.");
        } catch (\Exception $e) {
            return redirect()->route('admin.fo.checkin')
                ->withInput()
                ->with('error', 'Gagal memproses penolakan: '.$e->getMessage());
        }
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
                    'status' => 'Confirmed',
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
