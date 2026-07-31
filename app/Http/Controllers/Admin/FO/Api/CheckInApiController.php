<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO\Api;

use App\Data\AdminFO\WalkInTicketData;
use App\Enums\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFO\ApiStoreWalkInTicketRequest;
use App\Http\Requests\AdminFO\CheckNikRequest;
use App\Http\Resources\AdminFO\NotificationResource;
use App\Http\Resources\AdminFO\VerifyBookingResource;
use App\Http\Resources\AdminFO\VisitorLookupResource;
use App\Models\Queue;
use App\Services\AdminFO\CheckInService;
use App\Services\AdminFO\VisitorLookupService;
use App\Services\AdminFO\WalkInTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * CheckInApiController — REST API endpoints untuk Front Office.
 *
 * Melayani request AJAX/Fetch dari halaman FO.
 */
final class CheckInApiController extends Controller
{
    public function __construct(
        protected CheckInService $checkInService,
        protected WalkInTicketService $ticketService
    ) {}

    /**
     * Verifikasi booking via API.
     * GET /api/fo/bookings/verify?code={booking_code}
     */
    public function verify(Request $request): JsonResponse|VerifyBookingResource
    {
        $code = trim((string) $request->query('code', ''));

        if (empty($code)) {
            return response()->json(['message' => 'Booking code is required.'], 400);
        }

        $booking = $this->checkInService->findBookingByCode($code);

        if (! $booking || ($booking->status !== QueueStatus::Booked->value && $booking->status !== QueueStatus::Booked)) {
            return response()->json(['message' => 'Booking not found or already verified.'], 404);
        }

        return new VerifyBookingResource($booking);
    }

    /**
     * Check-in booking via API.
     * POST /api/fo/bookings/{booking}/checkin
     */
    public function checkIn(Request $request, Queue $booking): JsonResponse
    {
        if ($booking->status !== QueueStatus::Booked->value && $booking->status !== QueueStatus::Booked) {
            return response()->json(['message' => 'Booking status is not Pending.'], 422);
        }

        try {
            $queue = $this->checkInService->approveCheckIn($booking);

            return response()->json([
                'success' => true,
                'id' => $queue->id,
                'queue_number' => $queue->queue_number,
                'status' => $queue->status->value ?? $queue->status,
                'booking_code' => $queue->booking_code,
                'purpose' => $queue->purpose,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Terbitkan tiket walk-in via API.
     * POST /api/fo/queues/walkin
     */
    public function walkIn(ApiStoreWalkInTicketRequest $request): JsonResponse
    {
        try {
            $nextDeptIds = $request->input('next_department_ids');
            if (is_array($nextDeptIds)) {
                $nextDeptIds = array_values(array_unique(array_filter(array_map('intval', $nextDeptIds), fn ($id) => $id > 0 && $id !== (int) $request->input('department_id'))));
            } else {
                $nextDeptIds = null;
            }

            // Map request parameters to WalkInTicketData DTO
            $dto = new WalkInTicketData(
                departmentId: (int) $request->input('department_id'),
                name: $request->input('name'),
                nik: $request->input('nik'),
                phone: $request->input('phone', '00000000000'),
                purpose: $request->input('purpose'),
                isPriority: (bool) $request->input('is_priority', false),
                nextDepartmentIds: $nextDeptIds
            );

            $queue = $this->ticketService->issueTicket($dto);

            return response()->json([
                'success' => true,
                'id' => $queue->id,
                'queue_number' => $queue->queue_number,
                'status' => $queue->status->value ?? $queue->status,
                'visitor_name' => $queue->user->name,
                'booking_code' => $queue->booking_code,
                'purpose' => $queue->purpose,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Check NIK via API.
     */
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

    public function notifications(Request $request): JsonResponse
    {
        $notifications = $request->user()->unreadNotifications()
            ->where('title', 'Booking Baru Masuk')
            ->latest()
            ->get();

        return response()->json([
            'notifications' => NotificationResource::collection($notifications),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markNotificationRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->unreadNotifications()->where('id', $id)->first();
        if ($notification) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
