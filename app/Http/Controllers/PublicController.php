<?php

namespace App\Http\Controllers;

use App\Models\Department as Instansi;
use App\Models\Queue;
use App\Models\Service as Layanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PublicController extends Controller
{
    public function index()
    {
        try {
            // Ambil jumlah total instansi aktif (menggunakan model Department sebagai Instansi)
            $totalInstansi = Schema::hasTable('departments') && Schema::hasColumn('departments', 'is_active')
                ? Instansi::where('is_active', true)->count()
                : (Schema::hasTable('departments') ? Instansi::count() : 12);

            // Ambil jumlah total layanan aktif (menggunakan model Service sebagai Layanan)
            $totalLayanan = Schema::hasTable('services') && Schema::hasColumn('services', 'is_active')
                ? Layanan::where('is_active', true)->count()
                : (Schema::hasTable('services') ? Layanan::count() : 85);

            // Perhitungan rata-rata waktu tunggu dari database (tabel queues)
            if (Schema::hasTable('queues')) {
                $completedQueues = Queue::where('status', 'Completed')
                    ->whereNotNull('called_at')
                    ->whereNotNull('created_at')
                    ->limit(100)
                    ->get(['created_at', 'called_at']);

                if ($completedQueues->isEmpty()) {
                    $rataWaktuTunggu = '15 Menit';
                } else {
                    $totalMinutes = 0;
                    $count = 0;
                    foreach ($completedQueues as $q) {
                        $created = Carbon::parse($q->created_at);
                        $called = Carbon::parse($q->called_at);
                        if ($called->greaterThanOrEqualTo($created)) {
                            $totalMinutes += $called->diffInMinutes($created);
                            $count++;
                        }
                    }
                    $avgMinutes = $count > 0 ? round($totalMinutes / $count) : 15;
                    $rataWaktuTunggu = $avgMinutes.' Menit';
                }
            } else {
                $rataWaktuTunggu = '< 15m';
            }
        } catch (\Exception $e) {
            // Fallback jika terjadi error (misalnya tabel belum di-migrate)
            $totalInstansi = 12;
            $totalLayanan = 85;
            $rataWaktuTunggu = '< 15m';
        }

        return view('pages.index', compact('totalInstansi', 'totalLayanan', 'rataWaktuTunggu'));
    }

    public function checkQueue()
    {
        return view('pages.check-queue');
    }
}
