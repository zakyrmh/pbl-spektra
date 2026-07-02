@extends('layouts.private')

@section('title', 'Dashboard - MPP Kota Sawahlunto')

@section('content')
    @php
        $role = Auth::user()->role;
        $role = $role instanceof \BackedEnum ? $role->value : ($role ?? 'pengunjung');
        if ($role === 'warga') $role = 'pengunjung';
    @endphp

    @if ($role === 'pengunjung')
        @include('dashboard.roles.pengunjung')
    @elseif ($role === 'admin_fo')
        @include('dashboard.roles.admin_fo')
    @elseif ($role === 'admin_gerai')
        @include('dashboard.roles.admin_gerai')
    @elseif ($role === 'super_admin')
        @include('dashboard.roles.super_admin')
    @else
        {{-- Unknown Role fallback --}}
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
            <div class="w-20 h-20 bg-surface-soft dark:bg-white/5 text-muted rounded-full flex items-center justify-center mb-4 border border-hairline dark:border-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-ink dark:text-white mb-2 font-display">Role Tidak Dikenali</h1>
            <p class="text-muted dark:text-on-dark-soft mb-8 font-body">Silakan hubungi administrator untuk mengatur hak akses Anda.</p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="h-11 px-6 bg-primary hover:bg-primary-hover text-white rounded-pill font-semibold transition-colors focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-accent-teal cursor-pointer">
                    Kembali ke Login
                </button>
            </form>
        </div>
    @endif
@endsection
