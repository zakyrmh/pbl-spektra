@extends('layouts.app')

@section('title', 'Cek Antrean - MPP Kota Sawahlunto')

@section('base_content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="fw-bold mb-4">Cek Status Antrean</h1>
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <p class="text-muted">Masukkan nomor antrean Anda untuk mengecek status.</p>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Nomor Antrean</label>
                            <input type="text" class="form-control" placeholder="Contoh: A001">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cek Sekarang</button>
                    </form>
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('home') }}" class="text-muted">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection
