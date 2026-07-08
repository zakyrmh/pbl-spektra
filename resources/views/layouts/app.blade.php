<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'MPP Kota Sawahlunto'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap"
        rel="stylesheet">

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">
</head>

<body class="antialiased font-body bg-canvas text-body">
    {{-- Pull-to-refresh element --}}
    <div id="pull-to-refresh" class="fixed top-0 left-0 right-0 z-50 flex flex-col items-center justify-center pointer-events-none transition-all duration-200" style="height: 60px; transform: translateY(-100%);">
        <div class="bg-white dark:bg-gray-800 shadow-md border border-hairline dark:border-white/10 rounded-full px-4 py-2 flex items-center justify-center gap-2 transform scale-90 opacity-0 transition-all duration-200" id="ptr-indicator">
            <svg id="ptr-icon" class="w-4 h-4 text-primary dark:text-accent-teal transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
            <svg id="ptr-spinner" class="w-4 h-4 text-primary dark:text-accent-teal animate-spin hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span id="ptr-text" class="text-[10px] font-bold text-ink dark:text-white uppercase tracking-wider font-display pr-1">Tarik untuk memperbarui</span>
        </div>
    </div>

    <div id="app">
        @yield('base_content')
    </div>
    <script>
        function copyToClipboard(text, successCallback) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    if (successCallback) successCallback();
                }).catch(err => {
                    console.error('Failed to copy using clipboard API: ', err);
                    fallbackCopyToClipboard(text, successCallback);
                });
            } else {
                fallbackCopyToClipboard(text, successCallback);
            }
        }

        function fallbackCopyToClipboard(text, successCallback) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                const successful = document.execCommand('copy');
                if (successful && successCallback) {
                    successCallback();
                }
            } catch (err) {
                console.error('Fallback copy failed: ', err);
            }
            document.body.removeChild(textArea);
        }

        function showToast(title, message, type = 'success') {
            let container = document.getElementById('custom-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'custom-toast-container';
                container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            let borderClr = '';
            let bgClr = 'bg-slate-900 text-white';
            let iconHtml = '';

            if (type === 'success') {
                borderClr = 'border-l-4 border-emerald-500';
                iconHtml = `<svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            } else if (type === 'warning') {
                borderClr = 'border-l-4 border-amber-500';
                iconHtml = `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
            } else if (type === 'danger' || type === 'error') {
                borderClr = 'border-l-4 border-red-500';
                iconHtml = `<svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            } else {
                borderClr = 'border-l-4 border-blue-500';
                iconHtml = `<svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
            }

            toast.className = `flex items-start gap-3 p-4 rounded-xl shadow-2xl border border-white/10 ${bgClr} ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
            toast.innerHTML = `
                <div class="shrink-0">${iconHtml}</div>
                <div class="flex-grow">
                    <p class="text-xs font-bold font-display">${title}</p>
                    <p class="text-[11px] text-gray-300 mt-1 font-body leading-relaxed">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                if (toast.isConnected) {
                    toast.classList.remove('translate-y-2', 'opacity-0');
                }
            }, 50);

            setTimeout(() => {
                if (toast.isConnected) {
                    toast.classList.add('opacity-0', 'translate-y-[-10px]');
                    setTimeout(() => {
                        if (toast.isConnected) toast.remove();
                    }, 300);
                }
            }, 4000);
        }

        function showSuccessToast(message) {
            showToast('Sukses', message, 'success');
        }

        // Pull to Refresh feature for mobile/touch devices
        document.addEventListener('DOMContentLoaded', () => {
            if (!('ontouchstart' in window)) return;

            let startY = 0;
            let currentY = 0;
            let pulling = false;
            const threshold = 70; // Pull distance in px to trigger refresh
            const maxPull = 100;  // Maximum pull distance in px

            const ptr = document.getElementById('pull-to-refresh');
            const ptrIndicator = document.getElementById('ptr-indicator');
            const ptrIcon = document.getElementById('ptr-icon');
            const ptrSpinner = document.getElementById('ptr-spinner');
            const ptrText = document.getElementById('ptr-text');

            if (!ptr || !ptrIndicator || !ptrIcon || !ptrSpinner || !ptrText) return;

            function getScrollContainer(target) {
                let el = target;
                while (el && el !== document.body && el !== document.documentElement) {
                    const overflowY = window.getComputedStyle(el).overflowY;
                    if ((overflowY === 'auto' || overflowY === 'scroll') && el.scrollHeight > el.clientHeight) {
                        return el;
                    }
                    el = el.parentElement;
                }
                return window;
            }

            let scrollContainer = window;

            window.addEventListener('touchstart', (e) => {
                const touch = e.touches[0];
                scrollContainer = getScrollContainer(touch.target);
                
                const scrollTop = scrollContainer === window ? window.scrollY : scrollContainer.scrollTop;
                if (scrollTop === 0) {
                    startY = touch.pageY;
                    pulling = true;
                } else {
                    pulling = false;
                }
            }, { passive: true });

            window.addEventListener('touchmove', (e) => {
                if (!pulling) return;

                const touch = e.touches[0];
                currentY = touch.pageY;
                const pullDistance = currentY - startY;

                if (pullDistance > 0) {
                    if (e.cancelable) e.preventDefault();

                    const y = Math.min(pullDistance * 0.4, maxPull);

                    ptr.style.transform = `translateY(${y - 60}px)`;
                    ptrIndicator.classList.remove('opacity-0', 'scale-90');
                    ptrIndicator.classList.add('opacity-100', 'scale-100');

                    const rotation = Math.min(pullDistance * 2.5, 180);
                    ptrIcon.style.transform = `rotate(${rotation}deg)`;

                    if (y >= threshold) {
                        ptrText.innerText = 'Lepaskan untuk memperbarui';
                        ptrIcon.style.color = '#10B981';
                    } else {
                        ptrText.innerText = 'Tarik untuk memperbarui';
                        ptrIcon.style.color = '';
                    }
                }
            }, { passive: false });

            window.addEventListener('touchend', () => {
                if (!pulling) return;
                pulling = false;

                const pullDistance = currentY - startY;
                const y = Math.min(pullDistance * 0.4, maxPull);

                if (y >= threshold) {
                    ptr.style.transform = 'translateY(20px)';
                    ptrIcon.classList.add('hidden');
                    ptrSpinner.classList.remove('hidden');
                    ptrText.innerText = 'Memperbarui...';

                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    ptr.style.transform = 'translateY(-100%)';
                    ptrIndicator.classList.add('opacity-0', 'scale-90');
                    ptrIndicator.classList.remove('opacity-100', 'scale-100');
                    setTimeout(() => {
                        ptrIcon.style.transform = 'rotate(0deg)';
                        ptrIcon.style.color = '';
                    }, 200);
                }
            });
        });
    </script>

    @if(session('success') || session('status') || session('error') || session('warning') || session('info'))
    @php
        $toastType = 'info';
        $toastMessage = '';
        
        if (session('success')) {
            $toastType = 'success';
            $toastMessage = session('success');
        } elseif (session('status')) {
            $toastType = 'success';
            $toastMessage = session('status');
        } elseif (session('error')) {
            $toastType = 'error';
            $toastMessage = session('error');
        } elseif (session('warning')) {
            $toastType = 'warning';
            $toastMessage = session('warning');
        } elseif (session('info')) {
            $toastType = 'info';
            $toastMessage = session('info');
        }
    @endphp
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed bottom-5 right-5 z-9999 max-w-sm w-full bg-canvas dark:bg-surface-dark-elevated border border-hairline dark:border-white/10 text-ink dark:text-white rounded-lg shadow-lg p-4 flex items-start gap-3"
        style="display: none;"
    >
        <div class="shrink-0 mt-0.5">
            @if($toastType === 'success')
                <svg class="w-5 h-5 text-accent-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($toastType === 'error')
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($toastType === 'warning')
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            @else
                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>

        <div class="flex-1">
            <p class="text-xs font-semibold leading-relaxed">
                {!! $toastMessage !!}
            </p>
        </div>

        <button @click="show = false" class="text-muted hover:text-ink dark:text-on-dark-soft dark:hover:text-white transition-colors cursor-pointer shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    @endif

    @stack('scripts')
</body>

</html>