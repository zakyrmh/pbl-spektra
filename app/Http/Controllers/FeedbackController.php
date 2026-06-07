<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    /**
     * Tampilkan formulir pengisian feedback & rating.
     * GET /feedback/create
     */
    public function create(Request $request): View|RedirectResponse
    {
        $queueId = $request->query('queue_id');

        if (! $queueId) {
            return redirect()->route('dashboard')
                ->with('error', 'Parameter nomor antrean tidak valid.');
        }

        $queue = Queue::with(['booking', 'visitor', 'feedback'])->find($queueId);

        if (! $queue) {
            return redirect()->route('dashboard')
                ->with('error', 'Data antrean tidak ditemukan.');
        }

        // 1. Cek apakah status antrean benar-benar 'Completed' (Selesai)
        if ($queue->status !== 'Completed') {
            return redirect()->route('dashboard')
                ->with('error', 'Ulasan hanya dapat diberikan untuk pelayanan yang telah selesai.');
        }

        // 2. Cek apakah antrean ini sudah pernah diberi feedback (One-review-per-ticket)
        if ($queue->feedback) {
            return redirect()->route('dashboard')
                ->with('warning', 'Akses Ditolak! Anda sudah mengisi ulasan untuk layanan ini.');
        }

        // 3. Hak Akses (RBAC)
        $user = Auth::user();
        $role = $user->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : $role;

        if ($roleValue === 'pengunjung') {
            // Pengunjung hanya bisa mengulas antreannya sendiri
            if (! $queue->booking || $queue->booking->user_id !== $user->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda tidak memiliki akses untuk mengulas antrean ini.');
            }
        } elseif ($roleValue === 'admin_fo') {
            // Front Office hanya bisa mengulas atas nama walk-in
            if ($queue->booking_id !== null) {
                return redirect()->route('dashboard')
                    ->with('error', 'Petugas Front Office hanya dapat mengisi ulasan untuk pengunjung walk-in.');
            }
        } else {
            // Role lain tidak diizinkan mengisi ulasan
            return redirect()->route('dashboard')
                ->with('error', 'Peran Anda tidak diizinkan untuk mengisi ulasan.');
        }

        return view('feedback.create', compact('queue'));
    }

    /**
     * Simpan data feedback & rating ke database.
     * POST /feedback
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'queue_id' => ['required', 'exists:queues,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Silakan pilih rating terlebih dahulu.',
            'rating.between' => 'Rating harus bernilai antara 1 sampai 5.',
        ]);

        $queueId = $request->input('queue_id');
        $queue = Queue::with(['booking', 'feedback'])->findOrFail($queueId);

        // Validasi Keamanan Ganda di Backend
        if ($queue->status !== 'Completed') {
            return redirect()->route('dashboard')
                ->with('error', 'Gagal: Pelayanan belum selesai.');
        }

        if ($queue->feedback) {
            return redirect()->route('dashboard')
                ->with('warning', 'Gagal: Ulasan untuk layanan ini sudah pernah dikirim.');
        }

        $user = Auth::user();
        $role = $user->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : $role;

        if ($roleValue === 'pengunjung') {
            if (! $queue->booking || $queue->booking->user_id !== $user->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Akses tidak sah.');
            }
        } elseif ($roleValue === 'admin_fo') {
            if ($queue->booking_id !== null) {
                return redirect()->route('dashboard')
                    ->with('error', 'Akses tidak sah untuk petugas.');
            }
        } else {
            return redirect()->route('dashboard')
                ->with('error', 'Akses tidak sah.');
        }

        // Simpan data feedback
        $feedback = Feedback::create([
            'queue_id' => $queue->id,
            'user_id' => $user->id,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        // Catat di ActivityLog
        $actorName = $roleValue === 'admin_fo' ? 'Petugas Front Office (atas nama walk-in)' : 'Pengunjung';
        ActivityLog::record(
            action: 'SUBMIT_FEEDBACK',
            modelType: 'Feedback',
            modelId: $feedback->id,
            description: "{$actorName} memberikan rating bintang {$feedback->rating} untuk nomor antrean {$queue->queue_number}.",
            actorUserId: $user->id
        );

        return redirect()->route('dashboard')
            ->with('success', 'Feedback berhasil dikirim, terima kasih!');
    }
}
