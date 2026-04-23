<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('private.dashboard');
    }

    public function manageQueue()
    {
        return view('private.dashboard'); // Sementara redirect ke dashboard
    }

    public function callNext(Request $request)
    {
        // TODO: Implementasi panggil antrean berikutnya
        return back()->with('success', 'Antrean berikutnya telah dipanggil.');
    }
}
