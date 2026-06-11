<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Tampilkan daftar notifikasi milik pengunjung.
     * GET /notifikasi
     */
    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi sebagai dibaca dan redirect ke form feedback jika ada antrean selesai.
     * GET /notifikasi/{notification}
     */
    public function show(Notification $notification): RedirectResponse
    {
        // Pastikan notifikasi milik user yang sedang login
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        // Cari antrean Completed milik user ini yang belum diberi feedback
        $userId = Auth::id();
        $unreviewedQueue = Queue::whereHas('booking', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('status', 'Completed')
            ->whereDoesntHave('feedback')
            ->orderBy('completed_at', 'desc')
            ->first();

        if ($unreviewedQueue) {
            return redirect()->route('feedback.create', ['queue_id' => $unreviewedQueue->id])
                ->with('info', 'Silakan isi ulasan untuk pelayanan Anda.');
        }

        return redirect()->route('dashboard')
            ->with('info', 'Notifikasi telah ditandai sebagai dibaca.');
    }
}
