<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\StoreWalkInTicketRequest;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WalkInTicketService
{
    /**
     * Ambil data instansi beserta layanan dan loket-nya untuk form.
     * Menggunakan nested eager loading untuk mencegah N+1.
     *
     * @return Collection<int, Department>
     */
    public function getFormData(): Collection
    {
        return Department::with(['services', 'counters'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Terbitkan nomor antrean walk-in menggunakan DB Transaction.
     * Menyimpan data ke tabel `visitors` dan `queues` secara atomik.
     *
     * @throws \Throwable
     */
    public function issueTicket(StoreWalkInTicketRequest $request): Queue
    {
        return DB::transaction(function () use ($request): Queue {
            // 1. Simpan data pengunjung walk-in ke tabel visitors
            $visitor = Visitor::create([
                'name' => $request->string('name')->toString(),
                'nik' => $request->string('nik')->toString(),
                'phone' => $request->string('phone')->toString(),
                'purpose' => $request->string('purpose')->toString(),
            ]);

            // 2. Hitung nomor urut dengan lockForUpdate untuk mencegah race condition
            $today = Carbon::today();
            $counterId = (int) $request->input('counter_id');

            $existingCount = Queue::where('counter_id', $counterId)
                ->whereDate('queue_date', $today)
                ->lockForUpdate()
                ->count();

            $nextNumber = $existingCount + 1;

            // 3. Ambil inisial instansi untuk prefix nomor antrean
            $counter = Counter::with('department')->findOrFail($counterId);
            $prefix = $counter->department->inisial;

            // Format: [INISIAL]-[001], contoh: DDK-001
            $queueNumber = $prefix.'-'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

            // 4. Simpan tiket antrean ke tabel queues
            $queue = Queue::create([
                'visitor_id' => $visitor->id,
                'counter_id' => $counterId,
                'service_id' => (int) $request->input('service_id'),
                'queue_number' => $queueNumber,
                'status' => 'Waiting',
                'queue_date' => $today,
            ]);

            // 5. Return dengan relasi yang sudah dimuat (no additional queries)
            return $queue->load(['visitor', 'counter.department', 'service']);
        });
    }
}
