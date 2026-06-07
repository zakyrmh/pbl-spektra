<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\QueueMonitorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueMonitorController extends Controller
{
    public function __construct(
        protected QueueMonitorService $monitorService
    ) {}

    /**
     * Display the Front Office Queue Monitor page or return JSON for live polling.
     */
    public function index(Request $request): View|JsonResponse
    {
        $data = $this->monitorService->getMonitorData();

        if ($request->wantsJson() || $request->query('json') === 'true') {
            $departmentsData = $data['departments']->map(function ($dept) {
                $waiting = $dept->queues->where('status', 'Waiting')->count();
                $serving = $dept->queues->where('status', 'Serving')->count();

                $density = 'Kosong';
                $densityClass = 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200/50 dark:border-white/5';
                $densityDot = 'bg-gray-400 dark:bg-gray-500';

                if ($waiting > 5) {
                    $density = 'Padat';
                    $densityClass = 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border border-red-200/50 dark:border-red-900/50';
                    $densityDot = 'bg-red-500';
                } elseif ($waiting > 0) {
                    $density = 'Lancar';
                    $densityClass = 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 border border-green-200/50 dark:border-green-900/50';
                    $densityDot = 'bg-green-500';
                }

                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'inisial' => $dept->inisial,
                    'description' => $dept->description,
                    'waiting_count' => $waiting,
                    'serving_count' => $serving,
                    'density' => $density,
                    'density_class' => $densityClass,
                    'density_dot' => $densityDot,
                ];
            });

            return response()->json([
                'metrics' => $data['metrics'],
                'departments' => $departmentsData,
            ]);
        }

        return view('admin.fo.monitor', [
            'metrics' => $data['metrics'],
            'departments' => $data['departments'],
        ]);
    }
}
