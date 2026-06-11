<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogPelayananController extends Controller
{
    /**
     * Tampilkan halaman Log Pelayanan.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $departmentId = $user->departments_id;

        // Ambil jenis layanan untuk dropdown filter
        $services = Service::where('department_id', $departmentId)->get();

        // Bangun query bookings lampau
        // Eager load: user, service, schedule
        $query = Booking::forDepartment($departmentId)
            ->with(['user', 'service', 'schedule'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('updated_at', 'desc');

        // Apply filters

        // 1. Date Range Filter
        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->input('end_date'));
        }

        // 2. Dropdown Layanan (Service)
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }

        // 3. Dropdown Status (Completed/Cancelled)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default: tampilkan status Completed dan Cancelled (bookings lampau)
            $query->whereIn('status', ['Completed', 'Cancelled']);
        }

        // 4. Search input (booking_code atau nama warga)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Hitung total untuk ringkasan berdasarkan filter saat ini
        $summaryQuery = clone $query;

        // Ringkasan total antrean sukses (Completed)
        $totalSuccess = (clone $summaryQuery)->where('status', 'Completed')->count();

        // Ringkasan total antrean batal (Cancelled)
        $totalCancelled = (clone $summaryQuery)->where('status', 'Cancelled')->count();

        // Ambil data bookings dengan pagination
        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.log-pelayanan', compact(
            'services',
            'bookings',
            'totalSuccess',
            'totalCancelled'
        ));
    }

    /**
     * Ekspor data Log Pelayanan ke CSV/Excel.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (! $user->departments_id) {
            abort(403, 'Anda tidak ditugaskan ke instansi mana pun.');
        }

        $departmentId = $user->departments_id;

        $query = Booking::forDepartment($departmentId)
            ->with(['user', 'service', 'schedule'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('updated_at', 'desc');

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->input('end_date'));
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->whereIn('status', ['Completed', 'Cancelled']);
        }
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->get();
        $filename = 'log-pelayanan-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom
            fputcsv($file, [
                'Tanggal',
                'Kode Booking',
                'Nama Warga',
                'Keperluan',
                'Layanan',
                'Jam Datang (Checked In)',
                'Jam Selesai/Batal',
                'Status',
                'Catatan (Alasan Batal)',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '-',
                    $booking->booking_code,
                    $booking->user ? $booking->user->name : '-',
                    $booking->purpose ?? '-',
                    $booking->service ? $booking->service->name : '-',
                    $booking->checked_in_at ? $booking->checked_in_at->format('H:i:s') : '-',
                    in_array($booking->status, ['Completed', 'Cancelled']) && $booking->updated_at
                        ? $booking->updated_at->format('H:i:s')
                        : '-',
                    $booking->status,
                    $booking->cancel_reason ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
