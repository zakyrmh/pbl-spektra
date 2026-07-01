<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class GuideController extends Controller
{
    /**
     * Tampilkan halaman Panduan MPP berisi syarat dokumen & info layanan.
     */
    public function index(): View
    {
        return view('guide.index');
    }
}
