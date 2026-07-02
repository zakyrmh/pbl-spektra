<?php

namespace App\Providers;

use App\Events\Public\FeedbackSubmitted;
use App\Listeners\Public\LogFeedbackActivity;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Event;
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
        Gate::policy(Queue::class, BookingPolicy::class);
        Gate::policy(Notification::class, NotificationPolicy::class);

        // ── Events & Listeners ───────────────────────────────────
        Event::listen(FeedbackSubmitted::class, LogFeedbackActivity::class);
    }
}
