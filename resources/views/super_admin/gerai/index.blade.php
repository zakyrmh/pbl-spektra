@extends('layouts.private')

@section('title', 'Konfigurasi Gerai & Loket - MPP Sawahlunto')

@section('content')
<div class="space-y-6 pb-16 font-body" x-data="{ activeTab: '{{ request('tab', 'gerai') }}' }">

    {{-- Top Metrics --}}
    @include('super_admin.gerai.components.metrics')

    {{-- Tabs Navigation & Actions (Mobile-First Layout Split) --}}
    <div class="border-b border-hairline dark:border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        {{-- Action Button (Full-width on Mobile) --}}
        <div class="pb-4">
            <button onclick="openAddGeraiModal()" class="w-full md:w-auto inline-flex items-center justify-center h-11 px-6 text-button font-semibold text-white bg-primary hover:bg-primary-hover active:scale-[0.98] rounded-pill shadow-md hover:shadow-lg transition-all duration-150 gap-2 cursor-pointer">
                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Gerai</span>
            </button>
        </div>
    </div>

    {{-- Content Tabs --}}
    <div>
        <div class="space-y-4">
            {{-- Desktop Layout Table --}}
            @include('super_admin.gerai.components.table')

            {{-- Mobile Layout Cards --}}
            @include('super_admin.gerai.components.cards')
        </div>
    </div>
</div>

{{-- MODAL SYSTEM --}}
@include('super_admin.gerai.components.modal')

@endsection
