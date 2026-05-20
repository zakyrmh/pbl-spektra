@extends('layouts.app')

@section('base_content')
    <div class="min-h-screen bg-gray-100 flex">
        <x-sidebar />

        <div class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden">
            <x-header />

            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </main>
        </div>
    </div>
@endsection
