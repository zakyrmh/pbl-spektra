<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Native Gates & Policies ──────────────────────────────
        // Daftarkan UserPolicy untuk model User.
        // Laravel akan otomatis memetakan method policy ke Gate ability.
        Gate::policy(User::class, UserPolicy::class);
    }
}
