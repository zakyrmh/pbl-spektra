<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'MPP Kota Sawahlunto'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
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

        function showSuccessToast(message) {
            let container = document.getElementById('custom-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'custom-toast-container';
                container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = 'flex items-center gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 bg-white dark:bg-gray-800 border-l-4 border-green-500 max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0';
            toast.innerHTML = `
                <div class="shrink-0">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-grow">
                    <p class="text-xs font-bold text-ink dark:text-white font-display">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
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
            }, 3000);
        }
    </script>
    @stack('scripts')
</body>

</html>
