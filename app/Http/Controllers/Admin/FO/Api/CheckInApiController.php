<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO\Api;

use App\Data\AdminFO\WalkInTicketData;
use App\Enums\QueueStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFO\ApiStoreWalkInTicketRequest;
use App\Http\Requests\AdminFO\CheckNikRequest;
use App\Http\Resources\AdminFO\VisitorLookupResource;
use App\Models\Queue;
use App\Services\AdminFO\CheckInService;
use App\Services\AdminFO\VisitorLookupService;
use App\Services\AdminFO\WalkInTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function verify(Request $request): JsonResponse
    {
        $code = trim((string) $request->query('code', ''));

        if (empty($code)) {
            return response()->json(['message' => 'Booking code is required.'], 400);
        }

        $booking = $this->checkInService->findBookingByCode($code);

        if (! $booking || ($booking->status !== QueueStatus::Booked->value && $booking->status !== QueueStatus::Booked)) {
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
                'name' => $booking->department?->name ?? '-',
            ],
            'service' => [
                'name' => $booking->purpose ?? '-',
            ],
        ]);
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
                'queue_number' => $queue->queue_number,
                'status' => $queue->status->value ?? $queue->status,
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
            // Map request parameters to WalkInTicketData DTO
            $dto = new WalkInTicketData(
                name: $request->input('name'),
                nik: $request->input('nik'),
                phone: $request->input('phone', '00000000000'),
                purpose: $request->input('purpose'),
                departmentId: (int) $request->input('department_id')
            );

            $queue = $this->ticketService->issueTicket($dto);

            return response()->json([
                'success' => true,
                'queue_number' => $queue->queue_number,
                'status' => $queue->status->value ?? $queue->status,
                'visitor_name' => $queue->user->name,
            ]);
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
}
