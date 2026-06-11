<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Tampilkan riwayat booking milik pengunjung yang login.
     */
    public function index(): View
    {
        $bookings = Auth::user()->bookings()
            ->with(['service.department', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('booking.index', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Tampilkan form pembuatan booking baru.
     */
    public function create(): View
    {
        // Generate/ensure schedules exist for the next 7 days with dynamic quota
        $this->generateSchedules();

        // Ambil hanya instansi yang aktif/buka
        $departments = Department::where('is_open', true)->with('services')->get();

        // Get schedules grouped by service to easily filter on client side
        $schedules = Schedule::where('date', '>=', now()->toDateString())
            ->where('is_open', true)
            ->whereHas('service.department', function ($query) {
                $query->where('is_open', true);
            })
            ->whereColumn('quota_used', '<', 'quota_total')
            ->orderBy('date', 'asc')
            ->get();

        return view('booking.create', [
            'departments' => $departments,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Simpan reservasi booking antrean mandiri.
     */
    public function store(Request $request): RedirectResponse
    {
        // Generate/ensure schedules exist for the next 7 days
        $this->generateSchedules();

        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'keperluan' => ['required', 'string', 'min:5', 'max:255'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'department_id.required' => 'Silakan pilih instansi / lembaga.',
            'department_id.exists' => 'Instansi terpilih tidak valid.',
            'keperluan.required' => 'Silakan ketik keperluan Anda.',
            'keperluan.min' => 'Keperluan harus minimal 5 karakter.',
            'keperluan.max' => 'Keperluan tidak boleh lebih dari 255 karakter.',
            'booking_date.required' => 'Silakan pilih tanggal booking.',
            'booking_date.date' => 'Format tanggal booking tidak valid.',
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh hari kemarin.',
        ]);

        try {
            // Dapatkan semua layanan untuk instansi terpilih
            $services = Service::where('department_id', $request->department_id)->pluck('id');

            // Cari jadwal yang tersedia (open & kuota masih ada) pada tanggal tersebut untuk layanan instansi tersebut
            $schedule = Schedule::whereIn('service_id', $services)
                ->whereDate('date', $request->booking_date)
                ->where('is_open', true)
                ->whereColumn('quota_used', '<', 'quota_total')
                ->first();

            if (! $schedule) {
                return back()
                    ->withInput()
                    ->withErrors(['booking_date' => 'Jadwal tidak tersedia atau kuota penuh pada tanggal terpilih untuk instansi ini.']);
            }

            $booking = $this->bookingService->createBooking(
                Auth::user(),
                (int) $schedule->service_id,
                (int) $schedule->id,
                $request->input('keperluan')
            );

            return redirect()
                ->route('booking.show', $booking)
                ->with('success', 'Reservasi antrean mandiri berhasil dibuat.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tampilkan halaman tiket digital booking.
     */
    public function show(Booking $booking): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Security check: Only the owner, FO admin, or Super Admin can view the ticket
        if ($booking->user_id !== $user->id && ! in_array($user->role->value, ['super_admin', 'admin_fo'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat tiket ini.');
        }

        // Calculate estimated queue position (number of pending/checked-in bookings before this one on the same date)
        $estimatedPosition = Booking::where('service_id', $booking->service_id)
            ->where('booking_date', $booking->booking_date)
            ->where('id', '<', $booking->id)
            ->whereIn('status', ['Pending', 'Checked-In'])
            ->count() + 1;

        return view('booking.show', [
            'booking' => $booking->load(['service.department', 'schedule']),
            'estimatedPosition' => $estimatedPosition,
        ]);
    }

    /**
     * Membuat atau memastikan jadwal operasional terbuat untuk 7 hari ke depan secara otomatis
     * dengan kuota harian yang dinamis dihitung berdasarkan durasi pelayanan loket.
     */
    private function generateSchedules(): void
    {
        $services = Service::all();
        $startDate = now();
        $daysToGenerate = 7;

        for ($i = 0; $i < $daysToGenerate; $i++) {
            $date = $startDate->copy()->addDays($i);

            // Lewati hari Sabtu dan Minggu
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($services as $service) {
                $department = $service->department;
                if ($department && $department->is_open) {
                    // Hitung durasi rata-rata pelayanan per pengunjung (Completed queues)
                    $queues = DB::table('queues')
                        ->where('service_id', $service->id)
                        ->where('status', 'Completed')
                        ->whereNotNull('called_at')
                        ->whereNotNull('completed_at')
                        ->select(['called_at', 'completed_at'])
                        ->get();

                    if ($queues->isEmpty()) {
                        $avgTimeMinutes = 15.0; // default 15 menit jika belum ada data pelayanan selesai
                    } else {
                        $totalSeconds = 0;
                        foreach ($queues as $q) {
                            $called = new Carbon($q->called_at);
                            $completed = new Carbon($q->completed_at);
                            $totalSeconds += $completed->diffInSeconds($called);
                        }
                        $avgTimeMinutes = ($totalSeconds / $queues->count()) / 60;
                    }

                    $avgTimeMinutes = max($avgTimeMinutes, 5.0); // minimal 5 menit pelayanan per pengunjung

                    $operationalMinutes = 420.0; // 7 jam kerja aktif (misal 08:00 - 15:00)
                    $quotaTotal = (int) floor($operationalMinutes / $avgTimeMinutes);

                    // Pastikan kuota harian bernilai minimal 5 untuk kelancaran antrean
                    $quotaTotal = max($quotaTotal, 5);

                    // Periksa apakah jadwal sudah ada untuk tanggal dan layanan ini
                    $schedule = Schedule::where('service_id', $service->id)
                        ->whereDate('date', $date->toDateString())
                        ->first();

                    if (! $schedule) {
                        Schedule::create([
                            'service_id' => $service->id,
                            'date' => $date->toDateString(),
                            'quota_total' => $quotaTotal,
                            'quota_used' => 0,
                            'is_open' => true,
                            'session_name' => 'Umum',
                        ]);
                    }
                }
            }
        }
    }
}
