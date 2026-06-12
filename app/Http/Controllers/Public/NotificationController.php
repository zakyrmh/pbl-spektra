<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Public\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     */
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Tampilkan daftar notifikasi milik pengunjung.
     * GET /notifikasi
     */
    public function index(): View
    {
        $notifications = $this->notificationService->getUserNotifications((int) Auth::id());

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai notifikasi sebagai dibaca dan redirect ke form feedback jika ada antrean selesai.
     * GET /notifikasi/{notification}
     */
    public function show(Notification $notification): RedirectResponse
    {
        // Authorize access using the view policy
        Gate::authorize('view', $notification);

        $userId = (int) Auth::id();
        $unreviewedQueue = $this->notificationService->markAsReadAndFindUnreviewedQueue($notification, $userId);

        if ($unreviewedQueue) {
            return redirect()->route('feedback.create', ['queue_id' => $unreviewedQueue->id])
                ->with('info', 'Silakan isi ulasan untuk pelayanan Anda.');
        }

        return redirect()->route('dashboard')
            ->with('info', 'Notifikasi telah ditandai sebagai dibaca.');
    }
}
