<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Penggunaan di route: middleware('role:super_admin')
     * Bisa multiple role: middleware('role:admin_fo,admin_gerai')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Jika belum login, lempar ke halaman login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();

        // Super Admin memiliki bypass ke semua halaman
        if ($user->hasRole(UserRole::SuperAdmin)) {
            return $next($request);
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
