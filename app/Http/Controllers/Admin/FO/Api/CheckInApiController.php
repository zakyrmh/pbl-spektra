<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO\Api;

use App\Events\QueueCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckNikRequest;
use App\Http\Resources\VisitorLookupResource;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Services\VisitorLookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CheckInApiController — REST API endpoints untuk Front Office.
 *
 * Melayani request AJAX/Fetch dari halaman FO.
 * Dipisahkan dari CheckInController (web) sesuai prinsip SRP.
 */
class CheckInApiController extends Controller
{
    /**
     * Verifikasi booking via API.
     * GET /api/fo/bookings/verify?code={booking_code}
     */
    public function verify(Request $request): JsonResponse
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
     * Check-in booking via API.
     * POST /api/fo/bookings/{booking}/checkin
     */
    public function checkIn(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->status !== 'Pending') {
            return response()->json(['message' => 'Booking status is not Pending.'], 422);
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
                    throw new \Exception('No counter available for this department.');
                }

                $queueNumber = $this->generateQueueNumber($counter, $today);

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
                    description: "Admin FO berhasil check-in booking {$booking->booking_code} atas nama {$booking->user->name}.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

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
     * Terbitkan tiket walk-in via API.
     * POST /api/fo/queues/walkin
     */
    public function walkIn(Request $request): JsonResponse
    {
        $request->validate([
            'nik' => ['required', 'string', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'service_name' => ['nullable', 'string'],
        ]);

        $today = now()->toDateString();

        try {
            $queue = DB::transaction(function () use ($request, $today) {
                $departmentId = $request->input('department_id');
                $serviceName = $request->input('service_name');

                // Cari service
                $service = $serviceName
                    ? Service::where('department_id', $departmentId)->where('name', $serviceName)->first()
                    : null;

                $service ??= Service::where('department_id', $departmentId)->first();

                // Cari counter
                $counter = Counter::where('department_id', $departmentId)->first();
                if (! $counter) {
                    throw new \Exception('No counter available for this department.');
                }

                // Get or create visitor
                $visitor = Visitor::firstOrCreate(
                    ['nik' => $request->input('nik')],
                    [
                        'name' => $request->input('name'),
                        'phone' => '00000000000',
                        'purpose' => $request->input('purpose'),
                    ]
                );

                if (! $visitor->wasRecentlyCreated) {
                    $visitor->update(['purpose' => $request->input('purpose'), 'name' => $request->input('name')]);
                }

                $queueNumber = $this->generateQueueNumber($counter, $today, 'W');

                $queue = Queue::create([
                    'booking_id' => null,
                    'visitor_id' => $visitor->id,
                    'counter_id' => $counter->id,
                    'service_id' => $service?->id,
                    'queue_number' => $queueNumber,
                    'status' => 'Waiting',
                    'queue_date' => $today,
                ]);

                ActivityLog::record(
                    action: 'WALKIN_TICKET',
                    modelType: 'Queue',
                    modelId: $queue->id,
                    description: "Admin FO mencetak tiket mandiri Walk-In ({$queueNumber}) tujuan {$counter->department->name} untuk {$visitor->name}.",
                    actorUserId: Auth::id(),
                );

                return $queue;
            });

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

    public function checkNik(CheckNikRequest $request, VisitorLookupService $lookupService): JsonResponse|VisitorLookupResource
    {
        $nik = $request->input('nik');
        $visitor = $lookupService->findByNik($nik);

        if (! $visitor) {
            return response()->json([
                'message' => 'Warga baru, silakan isi data manual',
                'is_found' => false,
            ], 404);
        }

        return new VisitorLookupResource($visitor);
    }

    /**
     * Generate queue number berdasarkan counter & tanggal.
     */
    private function generateQueueNumber(Counter $counter, string $today, string $fallbackPrefix = 'Q'): string
    {
        $existingCount = Queue::where('counter_id', $counter->id)
            ->whereDate('queue_date', $today)
            ->lockForUpdate()
            ->count();

        $nextNumber = $existingCount + 1;
        $prefix = $counter->department->inisial ?: $fallbackPrefix;

        return $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
