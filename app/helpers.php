<?php

declare(strict_types=1);

use App\Models\User;

if (! function_exists('get_dashboard_route')) {
    /**
     * Get dashboard route name based on user role.
     */
    function get_dashboard_route(?User $user = null): string
    {
        /** @var User|null $user */
        $user = $user ?: auth()->user();
        if (! $user) {
            return 'login';
        }

        $role = $user->role;
        $roleValue = $role instanceof BackedEnum ? $role->value : $role;
        $roleValue = $roleValue ?: 'pengunjung';

        return match ($roleValue) {
            'super_admin' => 'superadmin.dashboard',
            'admin_fo' => 'admin_fo.dashboard',
            'admin_gerai' => 'admin_gerai.dashboard',
            'pengunjung' => 'visitor.dashboard',
            default => 'login',
        };
    }
}

if (! function_exists('get_dashboard_url')) {
    /**
     * Get dashboard URL based on user role.
     */
    function get_dashboard_url(?User $user = null): string
    {
        $user = $user ?: auth()->user();
        if (! $user) {
            return route('login');
        }

        return route(get_dashboard_route($user));
    }
}
