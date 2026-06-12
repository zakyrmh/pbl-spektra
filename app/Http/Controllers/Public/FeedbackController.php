<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Exceptions\Public\FeedbackValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreFeedbackRequest;
use App\Services\Public\FeedbackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class FeedbackController extends Controller
{
    /**
     * FeedbackController constructor.
     */
    public function __construct(
        protected FeedbackService $feedbackService
    ) {}

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

        try {
            $queue = $this->feedbackService->validateQueueForFeedback((int) $queueId, Auth::user());

            return view('feedback.create', compact('queue'));
        } catch (FeedbackValidationException $e) {
            return redirect()->route('dashboard')
                ->with($e->getAlertType(), $e->getMessage());
        }
    }

    /**
     * Simpan data feedback & rating ke database.
     * POST /feedback
     */
    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        $queueId = (int) $request->input('queue_id');

        try {
            $queue = $this->feedbackService->validateQueueForFeedback($queueId, Auth::user());
            $this->feedbackService->storeFeedback(
                $queue,
                (int) $request->input('rating'),
                $request->input('comment'),
                Auth::user()
            );

            return redirect()->route('dashboard')
                ->with('success', 'Feedback berhasil dikirim, terima kasih!');
        } catch (FeedbackValidationException $e) {
            return redirect()->route('dashboard')
                ->with($e->getAlertType(), $e->getMessage());
        }
    }
}
