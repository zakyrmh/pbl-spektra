@extends('layouts.app')

@section('base_content')
    <div class="min-h-screen bg-surface-soft dark:bg-surface-dark flex">
        <x-sidebar />

        <div class="flex-1 min-w-0 flex flex-col h-screen overflow-hidden md:pl-sidebar">
            <x-header />

            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </main>
        </div>
    </div>
@endsection
