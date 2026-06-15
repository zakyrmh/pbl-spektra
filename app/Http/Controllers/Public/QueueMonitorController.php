<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\MonitorFilterRequest;
use App\Http\Resources\Public\ActiveQueueResource;
use App\Services\AdminFO\QueueMonitorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class QueueMonitorController extends Controller
{
    public function __construct(
        protected QueueMonitorService $monitorService
    ) {}

    /**
     * Display the Front Office Queue Monitor page or return JSON for live polling.
     */
    public function index(MonitorFilterRequest $request): View|JsonResponse
    {
        $data = $this->monitorService->getMonitorData();

        if ($request->wantsJson() || $request->query('json') === 'true') {
            return response()->json([
                'metrics' => [
                    'total_waiting' => $data->totalWaiting,
                    'total_serving' => $data->totalServing,
                    'average_wait_time' => $data->averageWaitTime,
                ],
                'departments' => $data->departments->map(fn ($dept) => $dept->toArray()),
            ]);
        }

        return view('admin.fo.monitor', $data->toViewArray());
    }

    /**
     * Tampilkan layar monitor antrean utama untuk publik.
     * GET /display
     */
    public function publicDisplay(): View
    {
        $departments = $this->monitorService->getPublicDisplayDepartments();

        $marqueeText = $this->monitorService->getMarqueeText();
        $marqueeActive = $this->monitorService->isMarqueeActive();

        return view('public.display', compact('departments', 'marqueeText', 'marqueeActive'));
    }

    /**
     * API JSON untuk live polling monitor antrean utama.
     * GET /api/display/data
     */
    public function publicDisplayData(): JsonResponse
    {
        $departments = $this->monitorService->getPublicDisplayDepartments();

        $marqueeText = $this->monitorService->getMarqueeText();
        $marqueeActive = $this->monitorService->isMarqueeActive();

        return response()->json([
            'counters' => ActiveQueueResource::collection($departments)->resolve(),
            'marquee_text' => $marqueeText,
            'marquee_active' => $marqueeActive,
        ]);
    }
}
