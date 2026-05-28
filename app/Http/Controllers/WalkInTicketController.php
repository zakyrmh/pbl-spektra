<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreWalkInTicketRequest;
use App\Services\WalkInTicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WalkInTicketController extends Controller
{
    public function __construct(
        protected WalkInTicketService $ticketService
    ) {}

    /**
     * Tampilkan form pencetakan tiket walk-in.
     */
    public function create(): View
    {
        $departments = $this->ticketService->getFormData();

        return view('admin.fo.print-ticket', [
            'departments' => $departments,
        ]);
    }

    /**
     * Proses penerbitan nomor antrean walk-in dan redirect ke halaman cetak.
     */
    public function store(StoreWalkInTicketRequest $request): RedirectResponse
    {
        $queue = $this->ticketService->issueTicket($request);

        return redirect()
            ->route('admin.fo.ticket.create')
            ->with('ticket', $queue)
            ->with('success', "Tiket antrean {$queue->queue_number} berhasil diterbitkan untuk {$queue->visitor->name}.");
    }
}
