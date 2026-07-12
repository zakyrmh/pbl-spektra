<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFO\VerifyQrCodeRequest;
use App\Http\Resources\AdminFO\ScanQrCodeResource;
use App\Services\AdminFO\ScanQrCodeService;
use Illuminate\Http\JsonResponse;

class ScanQrCodeController extends Controller
{
    public function __construct(
        protected ScanQrCodeService $service
    ) {}

    /**
     * Handle the incoming scan QR request.
     * POST /api/fo/scan-qr
     */
    public function __invoke(VerifyQrCodeRequest $request): JsonResponse
    {
        $code = $request->input('code');
        $status = $request->input('status', 'Checked-In');

        try {
            $queue = $this->service->processQrCode($code, $status);

            return response()->json([
                'success' => true,
                'message' => 'Status antrean berhasil diperbarui.',
                'data' => new ScanQrCodeResource($queue),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
