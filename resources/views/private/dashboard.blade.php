@extends('layouts.private')

@section('title', 'Dashboard - MPP Kota Sawahlunto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Selamat Datang, {{ Auth::user()->name ?? 'Petugas' }}!</h4>
        <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
    </div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm">
            Logout
        </button>
    </form>
</div>

{{-- Alert notifikasi --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Kartu Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total Antrean Hari Ini</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <span class="fs-2 text-success">🎫</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Sedang Dilayani</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <span class="fs-2 text-primary">⚡</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Menunggu</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <span class="fs-2 text-warning">⏳</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Selesai</p>
                        <h3 class="fw-bold mb-0">0</h3>
                    </div>
                    <span class="fs-2 text-success">✅</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Antrean --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-semibold mb-0">Daftar Antrean Hari Ini</h6>
    </div>
    <div class="card-body">
        <div class="text-center text-muted py-4">
            <p class="mb-0">Belum ada data antrean.</p>
        </div>
    </div>
</div>
@endsection
