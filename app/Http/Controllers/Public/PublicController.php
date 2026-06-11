<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Queue;

final class PublicController extends Controller
{
    public function index()
    {
        $totalInstansi = Department::where('is_open', true)->count();

        $avgSeconds = null;
        if (config('database.default') === 'sqlite') {
            $avgSeconds = Queue::where('status', 'Completed')
                ->whereNotNull('called_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(strftime("%s", completed_at) - strftime("%s", called_at)) as avg_duration')
                ->value('avg_duration');
        } else {
            $avgSeconds = Queue::where('status', 'Completed')
                ->whereNotNull('called_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, called_at, completed_at)) as avg_duration')
                ->value('avg_duration');
        }

        $rataWaktuTunggu = '0 menit';
        if ($avgSeconds !== null) {
            $avgMinutes = (int) round((float) $avgSeconds / 60);
            $rataWaktuTunggu = $avgMinutes.' menit';
        }

        return view('pages.index', compact('totalInstansi', 'rataWaktuTunggu'));
    }

    public function checkQueue()
    {
        return view('pages.check-queue');
    }
}
