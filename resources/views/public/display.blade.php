@extends('layouts.app')

@section('title', 'Monitor Antrean Utama - MPP Kota Sawahlunto')

@section('base_content')
<div class="min-h-screen bg-surface-dark text-white flex flex-col justify-between overflow-hidden p-6 font-display">
    
    <!-- Header Monitor -->
    <div class="flex items-center justify-between border-b border-white/10 pb-4">
        <div class="flex items-center gap-4">
            <!-- Simbol/Logo (Inline SVG Lambang untuk estetika premium) -->
            <div class="w-12 h-12 bg-accent-teal/10 text-accent-teal rounded-xl flex items-center justify-center border border-accent-teal/20">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight">Mal Pelayanan Publik (MPP)</h1>
                <p class="text-xs text-on-dark-soft uppercase font-semibold tracking-widest">Kota Sawahlunto</p>
            </div>
        </div>
        <div class="text-right">
            <div class="text-2xl font-bold font-mono text-accent-teal" id="liveClock">00:00:00</div>
            <div class="text-[10px] text-on-dark-soft font-semibold uppercase tracking-wider mt-0.5" id="liveDate">Hari, Tanggal</div>
        </div>
    </div>

    <!-- Grid Loket -->
    <div class="grow grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 py-6 overflow-y-auto" id="monitorGrid">
        @foreach($departments as $department)
        @php
            $activeQueue = $department->queues->first();
        @endphp
        <div class="bg-surface-dark-elevated rounded-xl border border-white/5 p-6 flex flex-col justify-between shadow-lg relative overflow-hidden transition-all duration-500 hover:border-white/15" data-counter-id="{{ $department->id }}">
            
            <!-- Loket Meta -->
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-on-dark-soft uppercase tracking-wider">{{ $department->name }}</span>
                    @if($department->status->value === 'istirahat')
                        <span class="px-2 py-0.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] font-bold rounded-md">Istirahat</span>
                    @elseif($department->status->value === 'tutup' || $department->status->value === 'nonaktif')
                        <span class="px-2 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-bold rounded-md">Tutup</span>
                    @else
                        <span class="px-2 py-0.5 bg-green-500/10 text-green-400 border border-green-500/20 text-[9px] font-bold rounded-md">Aktif</span>
                    @endif
                </div>
                <h3 class="text-base font-bold text-white mt-1">Loket {{ $department->nomor_loket }}</h3>
            </div>

            <!-- Giant Active Number -->
            <div class="py-6 flex flex-col items-center justify-center text-center">
                <span class="text-[10px] font-bold text-on-dark-soft uppercase tracking-wider">Nomor Antrean</span>
                <span class="text-5xl md:text-6xl font-extrabold text-accent-teal font-mono tracking-tight my-2 active-number" data-current-val="{{ $activeQueue ? $activeQueue->queue_number : '-' }}">
                    {{ $activeQueue ? $activeQueue->queue_number : '-' }}
                </span>
                <span class="text-[9px] px-2 py-0.5 rounded-md bg-white/5 text-on-dark-soft border border-white/5 uppercase font-semibold">
                    {{ $activeQueue ? 'Sedang Dilayani' : 'Kosong' }}
                </span>
            </div>

            <!-- Background subtle watermark -->
            <div class="absolute -right-4 -bottom-4 opacity-[0.02] pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Ticker Marquee Footer -->
    @if($marqueeActive)
    <div class="bg-surface-dark-elevated border border-white/5 rounded-xl p-4 flex items-center gap-4 relative overflow-hidden shadow-lg select-none">
        <div class="shrink-0 bg-primary/20 text-accent-teal px-3 py-1.5 rounded-md text-xs font-bold border border-primary/30 uppercase tracking-wider z-10">
            Pengumuman
        </div>
        <div class="grow overflow-hidden relative w-full">
            <!-- Scrolling text -->
            <div class="whitespace-nowrap text-sm text-on-dark-soft animate-[marquee_25s_linear_infinite] inline-block hover:[animation-play-state:paused]" id="marqueeText">
                {{ $marqueeText }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Audio chime & Sound logic -->
<audio id="bellAudio" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav" preload="auto"></audio>

<!-- CSS variables overrides & Keyframe animations for TailwindCSS 4 -->
<style>
    @keyframes marquee {
        0% { transform: translate3d(100%, 0, 0); }
        100% { transform: translate3d(-100%, 0, 0); }
    }
    
    /* Pulse glow animation for newly called queue numbers */
    .glow-pulse {
        animation: glowPulse 2s infinite ease-in-out;
    }
    @keyframes glowPulse {
        0%, 100% { text-shadow: 0 0 0px rgba(41, 171, 226, 0); transform: scale(1); }
        50% { text-shadow: 0 0 15px rgba(41, 171, 226, 0.6); transform: scale(1.08); color: #FFFFFF; }
    }
</style>

@push('scripts')
<script>
    // Live Clock & Date Update
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').textContent = `${hrs}:${mins}:${secs}`;
    }

    function updateDate() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const now = new Date();
        
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();

        document.getElementById('liveDate').textContent = `${dayName}, ${date} ${monthName} ${year}`;
    }

    setInterval(updateClock, 1000);
    updateClock();
    updateDate();

    // Sound alert
    function playSound() {
        const audio = document.getElementById('bellAudio');
        if (audio) {
            audio.currentTime = 0;
            audio.play().catch(e => console.log('Audio blocked. Click the page to enable sound.'));
        }
    }

    // Voice announcement using Web Speech API (Indonesian)
    function announceQueue(queueNumber, counterName) {
        if (!('speechSynthesis' in window)) return;

        // Clean queue number reading (e.g. read "A-005" as "A kosong kosong lima" or "A lima")
        // Separate letters and numbers for clearer speech
        const parts = queueNumber.split('-');
        let numberText = '';
        if (parts.length > 1) {
            const letter = parts[0];
            const digits = parts[1].split('').map(d => d === '0' ? 'kosong' : d).join(' ');
            numberText = `${letter}, ${digits}`;
        } else {
            numberText = queueNumber;
        }

        // Format: "Nomor antrean A, kosong kosong lima. Silakan menuju Loket 01"
        const cleanCounterName = counterName.replace('Loket', 'Loket ');
        const text = `Nomor antrean. ${numberText}. Silakan menuju. ${cleanCounterName}.`;

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.85; // slightly slower for clarity
        utterance.pitch = 1.0;

        // Try to find an Indonesian voice
        const voices = window.speechSynthesis.getVoices();
        const idVoice = voices.find(voice => voice.lang.includes('id'));
        if (idVoice) utterance.voice = idVoice;

        // Play chime sound, then speak 1 second later
        playSound();
        setTimeout(() => {
            window.speechSynthesis.speak(utterance);
        }, 1100);
    }

    // Store state of currently serving queue numbers to detect changes
    let lastServingState = {};

    // Initial load
    document.querySelectorAll('#monitorGrid > div').forEach(card => {
        const id = card.getAttribute('data-counter-id');
        const numSpan = card.querySelector('.active-number');
        const numberVal = numSpan ? numSpan.getAttribute('data-current-val') : '-';
        lastServingState[id] = numberVal;
    });

    // AJAX Polling
    function pollDisplayData() {
        fetch('{{ route("display.data") }}')
            .then(res => res.json())
            .then(data => {
                // Update Marquee Text
                const marquee = document.getElementById('marqueeText');
                if (marquee && data.marquee_text) {
                    marquee.textContent = data.marquee_text;
                }

                // Update Counters
                const grid = document.getElementById('monitorGrid');
                
                data.counters.forEach(c => {
                    let card = document.querySelector(`[data-counter-id="${c.counter_id}"]`);
                    
                    if (card) {
                        const numSpan = card.querySelector('.active-number');
                        const statusBadge = card.querySelector('span[class*="border-"]');
                        const statusPill = card.querySelector('span[class*="bg-white/5"]');
                        
                        // Check if status changed
                        if (c.status === 'istirahat') {
                            statusBadge.className = 'px-2 py-0.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[9px] font-bold rounded-md';
                            statusBadge.textContent = 'Istirahat';
                        } else if (c.status === 'nonaktif') {
                            statusBadge.className = 'px-2 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[9px] font-bold rounded-md';
                            statusBadge.textContent = 'Tutup';
                        } else {
                            statusBadge.className = 'px-2 py-0.5 bg-green-500/10 text-green-400 border border-green-500/20 text-[9px] font-bold rounded-md';
                            statusBadge.textContent = 'Aktif';
                        }

                        // Check if queue number changed
                        const oldNum = lastServingState[c.counter_id] || '-';
                        const newNum = c.active_number;

                        if (oldNum !== newNum) {
                            lastServingState[c.counter_id] = newNum;
                            numSpan.textContent = newNum;
                            numSpan.setAttribute('data-current-val', newNum);
                            
                            if (newNum !== '-') {
                                statusPill.textContent = 'Sedang Dilayani';
                                
                                // Glow animation
                                numSpan.classList.add('glow-pulse');
                                setTimeout(() => {
                                    numSpan.classList.remove('glow-pulse');
                                }, 8000); // Pulse for 8 seconds

                                // Announce newly called number!
                                announceQueue(newNum, c.counter_name);
                            } else {
                                statusPill.textContent = 'Kosong';
                            }
                        }
                    }
                });
            })
            .catch(err => console.error('Polling error:', err));
    }

    // Enable speech synthesis voice loading
    if ('speechSynthesis' in window) {
        window.speechSynthesis.getVoices();
        if (window.speechSynthesis.onvoiceschanged !== undefined) {
            window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
        }
    }

    // Click anywhere to enable Web Audio (browsers block autoplay sounds/speech until click)
    document.body.addEventListener('click', () => {
        console.log("Audio contexts enabled.");
    }, { once: true });

    // Poll every 5 seconds (REQ-6.2)
    setInterval(pollDisplayData, 5000);
</script>
@endpush
