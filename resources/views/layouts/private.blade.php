@extends('layouts.app')

@section('base_content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">
                🏛️ MPP Sawahlunto
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-white-50 small">{{ Auth::user()->name ?? 'Petugas' }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>
@endsection

