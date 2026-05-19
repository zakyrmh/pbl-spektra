@extends('layouts.app')

@section('base_content')
    <div class="flex h-screen bg-gray-50 overflow-hidden font-sans">

        {{-- Sidebar Component --}}
        <x-sidebar />

        <div class="flex flex-col flex-1 w-full overflow-hidden">
            
            {{-- Header Component --}}
            <x-header />

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto bg-gray-50/50 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

        </div>
    </div>
@endsection
