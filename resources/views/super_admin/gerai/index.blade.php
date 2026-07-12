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

<script>
    function showErrorToast(message) {
        let container = document.getElementById('custom-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'custom-toast-container';
            container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none';
            document.body.appendChild(container);
        }
        
        const toast = document.createElement('div');
        toast.className = 'flex items-center gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 bg-white dark:bg-gray-800 border-l-4 border-red-500 max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0';
        toast.innerHTML = `
            <div class="shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="flex-grow">
                <p class="text-xs font-bold text-ink dark:text-white font-display">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;
        container.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }
        }, 50);
        
        // Auto remove
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(() => {
                    if (toast.isConnected) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 5000);
    }
</script>

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($errors->all() as $error)
                showErrorToast("{{ $error }}");
            @endforeach
        });
    </script>
@endif

@endsection
