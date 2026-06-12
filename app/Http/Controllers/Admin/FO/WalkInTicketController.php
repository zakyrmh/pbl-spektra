<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\FO;

use App\Data\WalkInTicketData;
use App\Http\Controllers\Controller;
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
        $dto = WalkInTicketData::fromRequest($request);
        $queue = $this->ticketService->issueTicket($dto);

        return redirect()
            ->route('admin.fo.ticket.create')
            ->with('ticket', $queue)
            ->with('success', "Tiket antrean <strong>{$queue->queue_number}</strong> berhasil diterbitkan untuk <strong>{$queue->user->name}</strong>.");
    }
}
