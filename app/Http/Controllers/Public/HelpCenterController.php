<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class HelpCenterController extends Controller
{
    /**
     * Tampilkan halaman Pusat Bantuan (Pusat Bantuan / Pusat Pengaduan).
     */
    public function index(): View
    {
        return view('help.index');
    }

    /**
     * Simpan data pengaduan warga ke database lokal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Pelayanan,Fasilitas,Sistem/Teknis,Lainnya'],
            'content' => ['required', 'string', 'min:10'],
        ], [
            'subject.required' => 'Subjek kendala wajib diisi.',
            'category.required' => 'Kategori kendala wajib dipilih.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'content.required' => 'Isi aduan wajib diisi.',
            'content.min' => 'Isi aduan minimal terdiri dari 10 karakter.',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Pengaduan Anda berhasil dikirim dan akan segera ditinjau oleh tim admin.');
    }
}
