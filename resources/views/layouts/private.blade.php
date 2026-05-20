@extends('layouts.app')

@section('base_content')
    <div class="min-h-screen bg-gray-100">

        {{-- Navbar --}}
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    {{-- Brand --}}
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2 text-blue-700 font-extrabold text-lg tracking-tight">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 21V9.75L12 3l9 6.75V21M9 21v-6h6v6" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01" />
                        </svg>
                        MPP Sawahlunto
                    </a>

                    {{-- Nav Links per Role --}}
                    <div class="hidden md:flex items-center gap-1">

                        {{-- Super Admin --}}
                        @if (Auth::user()->role === 'super_admin')
                            <a href="{{ route('dashboard') }}"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Dashboard
                            </a>
                            <a href="#"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Manajemen Pengguna
                            </a>
                            <a href="#"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Laporan
                            </a>
                        @endif

                        {{-- Admin FO --}}
                        @if (Auth::user()->role === 'admin_fo')
                            <a href="{{ route('dashboard') }}"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Dashboard
                            </a>
                            <a href="#"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Check-in Pengunjung
                            </a>
                        @endif

                        {{-- Admin Gerai --}}
                        @if (Auth::user()->role === 'admin_gerai')
                            <a href="{{ route('dashboard') }}"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Dashboard
                            </a>
                            <a href="{{ route('antrean.index') }}"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Kelola Antrean
                            </a>
                        @endif

                        {{-- Pengunjung --}}
                        @if (Auth::user()->role === 'pengunjung')
                            <a href="{{ route('dashboard') }}"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Dashboard
                            </a>
                            <a href="#"
                                class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                Reservasi Saya
                            </a>
                        @endif

                    </div>

                    {{-- User Info + Logout --}}
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-700">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-400 capitalize">
                                {{ str_replace('_', ' ', Auth::user()->role) }}
                            </p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-lg transition">
                                Logout
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

    </div>
@endsection
