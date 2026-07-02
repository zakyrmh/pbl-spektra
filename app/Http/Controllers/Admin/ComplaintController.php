<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ComplaintController extends Controller
{
    /**
     * Tampilkan daftar semua pengaduan warga.
     */
    public function index(): View
    {
        $complaints = Complaint::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Perbarui status pengaduan warga.
     */
    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Pending,Processing,Resolved'],
        ], [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $complaint->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Status pengaduan berhasil diperbarui menjadi: '.$validated['status']);
    }
}
