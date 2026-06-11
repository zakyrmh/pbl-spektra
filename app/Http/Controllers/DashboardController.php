<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Dashboard\AdminFoDashboardController;
use App\Http\Controllers\Dashboard\AdminGeraiDashboardController;
use App\Http\Controllers\Dashboard\PengunjungDashboardController;
use App\Http\Controllers\Dashboard\SuperAdminDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DashboardController — Role-based dispatcher.
 *
 * Controller ini berfungsi sebagai entry point tunggal untuk route /dashboard.
 * Seluruh logika dashboard di-delegate ke controller per-role masing-masing
 * di namespace App\Http\Controllers\Dashboard.
 */
class DashboardController extends Controller
{
    public function index(Request $request): mixed
    {
        $role = Auth::user()->role;
        $role = $role instanceof \BackedEnum ? $role->value : ($role ?? 'pengunjung');

        return match ($role) {
            'super_admin' => app(SuperAdminDashboardController::class)->index(),
            'admin_fo' => app(AdminFoDashboardController::class)->index(),
            'admin_gerai' => app(AdminGeraiDashboardController::class)->index(),
            default => app(PengunjungDashboardController::class)->index(),
        };
    }
}
