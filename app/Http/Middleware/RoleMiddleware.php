<?php

namespace App\Http\Middleware;

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

        // Jika role user tidak ada di daftar role yang diizinkan
        if (! in_array(Auth::user()->role, $roles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
