@extends('layouts.app')

@section('title', 'Beranda - MPP Kota Sawahlunto')

@section('base_content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Mal Pelayanan Publik Kota Sawahlunto</h1>
        <p class="lead text-muted">Sistem Antrean Digital - Pelayanan Cepat, Mudah, dan Transparan</p>
        <a href="{{ route('public.check') }}" class="btn btn-primary me-2">Cek Antrean</a>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Login Petugas</a>
    </div>
</div>
@endsection
