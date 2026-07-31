<!-- HTML5 QR Code Library (latest via jsDelivr CDN) -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>
<!-- JavaScript Logic for FO -->
<script>
    // Stats state
    let foStats = {
        antreanFO: {{ $todayFoQueueCount ?? 0 }},
        tiketDicetak: {{ $todayTotalPrintedTickets ?? 0 }}
    };

    // Global variable to store verified booking ID
    let currentVerifiedBookingId = null;

    // Live clock
    function updateClock() {
        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateStr = d.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short'
        });
        const clockEl = document.getElementById('fo-live-clock');
        if (clockEl) {
            clockEl.innerText = `${dateStr} | ${timeStr}`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateClock();
        setInterval(updateClock, 1000);
    });

    // Verification module
    async function verifyBookingCode() {
        const input = document.getElementById('txtBookingCode');
        const code = input.value.trim().toUpperCase();

        if (code === '') {
            alert("Silakan masukkan kode booking terlebih dahulu!");
            return;
        }

        try {
            const response = await fetch(`/api/fo/bookings/verify?code=${code}`);

            if (!response.ok) {
                if (response.status === 404) {
                    createToast('Tiket Tidak Ditemukan', `Kode booking ${code} tidak terdaftar di database.`,
                        'warning');
                } else {
                    createToast('Error Sistem', `Gagal memverifikasi tiket (Status: ${response.status}).`,
                        'warning');
                }
                document.getElementById('pnlVerifyDetails').classList.add('hidden');
                currentVerifiedBookingId = null;
                return;
            }

            const data = await response.json();
            const booking = data.data || data;

            // Map JSON response to DOM elements
            document.getElementById('verifyTicketCode').innerText = booking.booking_code || code;
            document.getElementById('verifyName').innerText = (booking.user && booking.user.name) ? booking.user.name : '-';
            document.getElementById('verifyNik').innerText = (booking.user && booking.user.nik) ? booking.user.nik : '-';

            // Nama instansi di-resolve dari relasi booking->counter->department
            const departmentName = (booking.department && booking.department.name) ||
                (booking.counter && booking.counter.department && booking.counter.department.name) ||
                '-';
            document.getElementById('verifyTenant').innerText = departmentName;

            // Layanan di-resolve dari counter name atau service name
            const serviceName = (booking.counter && booking.counter.name) ||
                (booking.service && booking.service.name) ||
                '-';
            document.getElementById('verifyService').innerText = serviceName;

            currentVerifiedBookingId = booking.id;

            const pnl = document.getElementById('pnlVerifyDetails');
            pnl.classList.remove('hidden');

            createToast('Tiket Ditemukan', `Kode booking ${code} berhasil diverifikasi. Silakan klik Konfirmasi.`,
                'info');
        } catch (error) {
            console.error('Error verifying booking:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk verifikasi tiket.', 'warning');
        }
    }

    // ==================== QR SCANNER BACKEND ====================
    let html5QrCode = null;
    let isScannerRunning = false;

    // ─── Camera Management ──────────────────────────────────────────────────

    async function loadAvailableCameras(alpineInstance) {
        alpineInstance.cameraLoadError = '';

        try {
            // Gunakan method static untuk mendapatkan daftar kamera
            const devices = await Html5Qrcode.getCameras();
            console.log('[QR Scanner] getCameras() result:', devices);

            if (devices && devices.length > 0) {
                alpineInstance.availableCameras = devices;
                alpineInstance.selectedCameraId = devices[0].id;

                // Setelah kamera ditemukan, ubah state ke scanning
                await alpineInstance.$nextTick();
                alpineInstance.scanState = 'scanning';
                await alpineInstance.$nextTick();

                // Delay tambahan untuk memastikan DOM terrender dengan dimensi yang benar
                setTimeout(() => {
                    startQrScannerBackend(alpineInstance.selectedCameraId, alpineInstance);
                }, 300);
            } else {
                alpineInstance.cameraLoadError = 'Tidak ada kamera terdeteksi';
                alpineInstance.scanState = 'error';
                alpineInstance.scanMessage = 'Tidak ada kamera yang terdeteksi di perangkat ini.';
            }
        } catch (err) {
            console.error('[QR Scanner] Error loading cameras:', err);

            if (err.toString().includes('NotAllowedError') || err.toString().includes('Permission')) {
                alpineInstance.cameraLoadError = 'Akses kamera ditolak. Izinkan akses kamera di browser.';
            } else if (err.toString().includes('NotFoundError')) {
                alpineInstance.cameraLoadError = 'Tidak ada kamera terdeteksi di perangkat.';
            } else {
                alpineInstance.cameraLoadError = 'Gagal memuat kamera: ' + (err.message || err);
            }

            alpineInstance.scanState = 'error';
            alpineInstance.scanMessage = alpineInstance.cameraLoadError;
        }
    }

    // Expose ke global agar bisa dipanggil dari Alpine template
    window.loadAvailableCameras = loadAvailableCameras;

    function startQrScannerBackend(cameraId, alpineInstance) {
        // Stop scanner yang sudah berjalan sebelum memulai baru
        if (html5QrCode && isScannerRunning) {
            html5QrCode
                .stop()
                .catch(() => {})
                .finally(() => {
                    html5QrCode.clear();
                    initAndStartScanner(cameraId, alpineInstance);
                });
        } else {
            initAndStartScanner(cameraId, alpineInstance);
        }
    }

    function initAndStartScanner(cameraId, alpineInstance) {
        const readerEl = document.getElementById('qr-reader');

        if (!readerEl) {
            console.error('[QR Scanner] #qr-reader element not found!');
            return;
        }

        // Pastikan elemen visible dan punya dimensi
        const rect = readerEl.getBoundingClientRect();
        console.log('[QR Scanner] Reader dimensions:', rect.width, 'x', rect.height);

        // Jika dimensi masih 0, coba lagi setelah 500ms (auto-retry)
        if (rect.width === 0 || rect.height === 0) {
            console.warn('[QR Scanner] Element has 0 dimensions, retrying in 500ms...');
            setTimeout(() => initAndStartScanner(cameraId, alpineInstance), 500);
            return;
        }

        try {
            // Hitung qrbox dinamis: max 75% dari container, min 150px, max 250px
            const maxWidth  = Math.floor(rect.width  * 0.75);
            const maxHeight = Math.floor(rect.height * 0.75);
            const qrboxSize = Math.max(150, Math.min(maxWidth, maxHeight, 250));

            console.log('[QR Scanner] Starting with qrbox:', qrboxSize, 'x', qrboxSize);

            html5QrCode = new Html5Qrcode('qr-reader');

            const config = {
                fps: 10,
                qrbox: { width: qrboxSize, height: qrboxSize },
                aspectRatio: 1.0,
                disableFlip: false,
            };

            html5QrCode
                .start(cameraId, config, onScanSuccess, onScanFailure)
                .then(() => {
                    isScannerRunning = true;
                    console.log('[QR Scanner] Camera started successfully');
                })
                .catch((err) => {
                    console.error('[QR Scanner] Start error:', err);
                    isScannerRunning = false;

                    if (alpineInstance) {
                        alpineInstance.scanState = 'error';

                        if (err.toString().includes('NotAllowedError')) {
                            alpineInstance.scanMessage = 'Akses kamera ditolak. Silakan izinkan akses kamera.';
                        } else if (err.toString().includes('NotFoundError')) {
                            alpineInstance.scanMessage = 'Kamera tidak ditemukan atau sedang digunakan aplikasi lain.';
                        } else if (err.toString().includes('NotReadableError')) {
                            alpineInstance.scanMessage = 'Kamera sedang digunakan oleh aplikasi lain.';
                        } else {
                            alpineInstance.scanMessage = 'Gagal memulai kamera: ' + (err.message || err);
                        }
                    }
                    createToast('Gagal Akses Kamera', 'Pastikan perizinan kamera aktif dan gunakan protokol HTTPS.', 'warning');
                });
        } catch (err) {
            console.error('[QR Scanner] Init error:', err);
            isScannerRunning = false;
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log('[QR Scanner] Scan success:', decodedText);

        // Hentikan scanner setelah berhasil scan
        stopQrScannerBackend();

        // Cari Alpine instance dari modal
        const xDataRoot = document.querySelector('[x-data]');
        if (xDataRoot) {
            const alpineComponent = Alpine.$data(xDataRoot);
            if (alpineComponent) {
                // Step 1: set processing lalu panggil API verifikasi terlebih dahulu
                alpineComponent.scanState = 'processing';
                alpineComponent.scanMessage = '';
                alpineComponent.scanResult = null;
                alpineComponent.scannedCode = decodedText;
                verifyQrCode(decodedText, alpineComponent);
            }
        }
    }

    function onScanFailure(error) {
        // Normal — terjadi setiap frame yang tidak mengandung QR code
        // Tidak perlu di-log agar tidak spam console
    }

    async function stopQrScannerBackend() {
        if (html5QrCode && isScannerRunning) {
            try {
                await html5QrCode.stop();
                html5QrCode.clear();
                console.log('[QR Scanner] Stopped successfully');
            } catch (err) {
                console.warn('[QR Scanner] Stop error:', err);
            }
        }
        isScannerRunning = false;
        html5QrCode = null;
    }

    // ─── QR Processing Logic (2-Step: Verify → Confirm) ─────────────────────

    /**
     * Verifikasi QR Code — cari data booking.
     * Segera TUTUP modal scanner dan tampilkan toast loading begitu terdeteksi,
     * lalu isi data ke panel 'Citizen verification details' di dashboard.
     */
    async function verifyQrCode(code, alpineData) {
        // ─── 1. TUTUP MODAL SCANNER SEGERA SETELAH TERDETEKSI ───────
        if (alpineData) {
            alpineData.qrScannerOpen = false;
            alpineData.scanState = 'idle';
            alpineData.scanResult = null;
            alpineData.scannedCode = '';
        }
        // Pastikan stream kamera berhenti untuk menghemat resource
        stopQrScannerBackend();

        // Tampilkan Toast pemberitahuan deteksi QR Code
        createToast('QR Terdeteksi', `Sedang memverifikasi kode booking ${code}...`, 'info');

        try {
            const response = await fetch(`/api/fo/bookings/verify?code=${encodeURIComponent(code)}`);

            if (response.status === 404) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Kode booking tidak ditemukan di database.`;
                createToast('QR Tidak Valid', errMsg, 'warning');
                return;
            }

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Gagal memverifikasi QR Code (Status: ${response.status}).`;
                createToast('Gagal Verifikasi', errMsg, 'warning');
                return;
            }

            const data = await response.json();
            const booking = data.data || data;

            // ─── 2. ISI DATA KE PANEL VERIFIKASI DI DASHBOARD ────────
            document.getElementById('verifyTicketCode').innerText = booking.booking_code || code;

            document.getElementById('verifyName').innerText = (booking.user && booking.user.name)
                ? booking.user.name
                : (booking.user_name || '-');

            document.getElementById('verifyNik').innerText = (booking.user && booking.user.nik)
                ? booking.user.nik
                : (booking.nik || '-');

            // Nama instansi di-resolve dari relasi booking->counter->department
            const departmentName = (booking.department && booking.department.name) ||
                (booking.counter && booking.counter.department && booking.counter.department.name) ||
                booking.department_name || '-';
            document.getElementById('verifyTenant').innerText = departmentName;

            // Layanan di-resolve dari counter name atau service name
            const serviceName = (booking.counter && booking.counter.name) ||
                (booking.service && booking.service.name) ||
                booking.service_name || '-';
            document.getElementById('verifyService').innerText = serviceName;

            // Simpan ID booking ke variable global agar tombol "Konfirmasi Kedatangan" bisa mengaksesnya
            currentVerifiedBookingId = booking.id || null;

            // ─── 3. TAMPILKAN PANEL VERIFIKASI ───────────────────────
            const pnl = document.getElementById('pnlVerifyDetails');
            pnl.classList.remove('hidden');

            // Opsional: Scroll halaman secara halus ke panel agar user langsung fokus ke sana
            setTimeout(() => {
                pnl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);

            // ─── 4. TAMPILKAN NOTIFIKASI SUKSES ─────────────────────
            createToast(
                'Tiket Ditemukan',
                `Kode booking ${code} berhasil dideteksi. Silakan klik Konfirmasi Kedatangan.`,
                'success'
            );

        } catch (error) {
            console.error('[QR] verifyQrCode error:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk memverifikasi QR Code.', 'warning');
        }
    }

    /**
     * Step 2: Eksekusi Check-In — dipanggil setelah admin menekan tombol 'Verifikasi'.
     * Memanggil API scan-qr/checkin dan memperbarui state ke 'success' atau 'error'.
     */
    async function executeCheckIn(code, alpineData) {
        try {
            const response = await fetch('/api/fo/scan-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code: code, status: 'Checked-In' })
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Gagal melakukan check-in (Status: ${response.status}).`;
                if (alpineData) { alpineData.scanState = 'error'; alpineData.scanMessage = errMsg; }
                createToast('Check-In Gagal', errMsg, 'warning');
                return;
            }

            const res  = await response.json();
            const data = res.data || res;

            // Gabungkan data sukses dengan rincian yang sudah ada
            const prevResult = alpineData ? alpineData.scanResult : {};
            const mergedResult = {
                ...(prevResult || {}),
                queue_number    : data.queue_number    || (prevResult && prevResult.queue_number)    || '-',
                user_name       : data.user_name       || (prevResult && prevResult.user_name)       || '-',
                department_name : data.department_name || (prevResult && prevResult.department_name) || '-',
                service_name    : data.service_name    || (prevResult && prevResult.service_name)    || '-',
                purpose         : data.purpose         || (prevResult && prevResult.purpose)         || '-',
            };

            if (alpineData) {
                alpineData.scanState  = 'success';
                alpineData.scanMessage = 'Check-in berhasil!';
                alpineData.scanResult  = mergedResult;
            }

            // Update metrik statistik lokal
            foStats.tiketDicetak++;
            const foStatAntrean = document.getElementById('foStatAntrean');
            if (foStatAntrean) foStatAntrean.innerText = foStats.antreanFO;
            const foStatTiket = document.getElementById('foStatTiket');
            if (foStatTiket) foStatTiket.innerText = foStats.tiketDicetak;

            addLiveFeedRow(
                mergedResult.user_name,
                mergedResult.queue_number,
                mergedResult.department_name,
                'Online Booking',
                data.status || 'Checked-In',
                data.id,
                mergedResult.booking_code || code,
                mergedResult.purpose
            );

            createToast(
                'Check-In Berhasil! ✓',
                `Warga ${mergedResult.user_name} (${mergedResult.queue_number}) telah tercatat hadir di ${mergedResult.department_name}.`,
                'success'
            );

        } catch (error) {
            console.error('[QR] executeCheckIn error:', error);
            if (alpineData) {
                alpineData.scanState  = 'error';
                alpineData.scanMessage = 'Tidak dapat terhubung ke server untuk menyelesaikan check-in.';
            }
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk menyelesaikan check-in.', 'warning');
        }
    }

    // ─── Manual QR Code Input Fallback ──────────────────────────────────────

    async function submitManualQrCode() {
        const input = document.getElementById('manualQrInput');
        const code = input ? input.value.trim() : '';
        if (!code) {
            createToast('Peringatan', 'Silakan masukkan kode QR / kode booking terlebih dahulu.', 'warning');
            return;
        }

        const alpineDiv = document.querySelector('[x-data]');
        let alpineData = null;
        if (alpineDiv) {
            alpineData = Alpine.$data(alpineDiv);
            // Step 1: set state processing & simpan kode
            alpineData.scanState  = 'processing';
            alpineData.scanMessage = '';
            alpineData.scanResult  = null;
            alpineData.scannedCode = code;
        }

        // Hentikan kamera jika sedang berjalan
        await stopQrScannerBackend();
        createToast('Memverifikasi Kode...', 'Sedang mencari data booking di server...', 'info');
        // Step 1: verifikasi dahulu, jangan langsung check-in
        await verifyQrCode(code, alpineData);
        if (input) input.value = '';
    }

    async function confirmCheckIn() {
        if (!currentVerifiedBookingId) {
            createToast('Peringatan', 'Silakan verifikasi kode booking terlebih dahulu.', 'warning');
            return;
        }

        const code = document.getElementById('verifyTicketCode').innerText;
        const name = document.getElementById('verifyName').innerText;
        const tenant = document.getElementById('verifyTenant').innerText;

        try {
            const response = await fetch(`/api/fo/bookings/${currentVerifiedBookingId}/checkin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Gagal konfirmasi check-in (Status: ${response.status}).`;
                createToast('Gagal Check-In', errMsg, 'warning');
                return;
            }

            const data = await response.json();

            // Modify local stats and update DOM
            if (foStats.antreanFO > 0) foStats.antreanFO--;
            foStats.tiketDicetak++;

            document.getElementById('foStatAntrean').innerText = foStats.antreanFO;
            document.getElementById('foStatTiket').innerText = foStats.tiketDicetak;

            // Hide details panel
            document.getElementById('pnlVerifyDetails').classList.add('hidden');
            document.getElementById('txtBookingCode').value = '';

            // Add to live feed with state parameter 'Waiting' (instead of 'Check-In FO')
            const finalCode = data.queue_number || data.ticket_code || code;
            addLiveFeedRow(name, finalCode, tenant, 'Online Booking', 'Waiting', data.id, data.booking_code, data.purpose);

            createToast('Check-In Sukses', `Warga ${name} (${finalCode}) telah check-in untuk loket ${tenant}.`,
                'success');

            // Reset global verified booking ID
            currentVerifiedBookingId = null;
        } catch (error) {
            console.error('Error confirming checkin:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk konfirmasi check-in.', 'warning');
        }
    }

    // Walk-In Module
    async function checkVisitorNik() {
        const inputNik = document.getElementById('txtWalkInNik');
        const inputName = document.getElementById('txtWalkInName');
        const inputPhone = document.getElementById('txtWalkInPhone');
        const nik = inputNik.value.trim();

        if (nik.length !== 16) {
            createToast('NIK Tidak Valid', 'Pastikan NIK terdiri dari 16 digit angka.', 'warning');
            return;
        }

        try {
            const response = await fetch(`/api/fo/visitors/check-nik?nik=${nik}`);

            if (response.status === 404) {
                inputName.value = '';
                inputName.disabled = false;
                inputPhone.value = '';
                inputPhone.disabled = false;
                inputName.focus();
                createToast('NIK Baru', 'Data tidak ditemukan. Silakan isi nama lengkap dan nomor telepon warga.',
                    'info');
                return;
            }

            if (!response.ok) {
                createToast('Gagal', 'Terjadi kesalahan saat memeriksa NIK.', 'warning');
                return;
            }

            const resData = await response.json();
            const data = resData.data || resData;

            if (data.is_found) {
                inputName.value = data.name;
                inputName.disabled = true;
                inputPhone.value = data.no_telp || '';
                inputPhone.disabled = true;
                createToast('NIK Ditemukan', `Data warga ${data.name} berhasil dimuat.`, 'success');
            } else {
                inputName.value = '';
                inputName.disabled = false;
                inputPhone.value = '';
                inputPhone.disabled = false;
                inputName.focus();
                createToast('NIK Baru', 'Data tidak ditemukan. Silakan isi nama lengkap dan nomor telepon warga.',
                    'info');
            }
        } catch (error) {
            console.error('Error checking NIK:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'warning');
        }
    }

    function resetWalkInForm() {
        document.getElementById('txtWalkInNik').value = '';
        const nameInput = document.getElementById('txtWalkInName');
        nameInput.value = '';
        nameInput.disabled = true;
        const phoneInput = document.getElementById('txtWalkInPhone');
        phoneInput.value = '';
        phoneInput.disabled = true;
        document.getElementById('selWalkInDept').value = '';
        document.querySelectorAll('.chk-next-dept').forEach(cb => cb.checked = false);
        document.getElementById('txtWalkInPurpose').value = '';
        const chkPriority = document.getElementById('chkWalkInPriority');
        if (chkPriority) chkPriority.checked = false;
    }

    async function printWalkInTicket() {
        const nik = document.getElementById('txtWalkInNik').value.trim();
        const name = document.getElementById('txtWalkInName').value.trim();
        const phone = document.getElementById('txtWalkInPhone').value.trim();
        const deptId = document.getElementById('selWalkInDept').value;
        const purpose = document.getElementById('txtWalkInPurpose').value.trim();

        // Collect Multi-Gerai Waterfall Queue (next_department_ids)
        const nextDeptCheckboxes = document.querySelectorAll('.chk-next-dept:checked');
        const nextDeptIds = Array.from(nextDeptCheckboxes).map(cb => parseInt(cb.value)).filter(id => id > 0 && id !== parseInt(deptId));

        if (nik.length !== 16) {
            createToast('Peringatan', 'NIK harus 16 digit.', 'warning');
            return;
        }
        if (!name) {
            createToast('Peringatan', 'Nama lengkap tidak boleh kosong.', 'warning');
            return;
        }
        if (!phone) {
            createToast('Peringatan', 'Nomor telepon wajib diisi.', 'warning');
            return;
        }
        const phoneRegex = /^(08[0-9]{8,13}|\+628[0-9]{8,11})$/;
        if (!phoneRegex.test(phone)) {
            createToast('Peringatan',
                'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 angka).', 'warning');
            return;
        }
        if (!deptId) {
            createToast('Peringatan', 'Silakan pilih Instansi Utama.', 'warning');
            return;
        }
        if (!purpose) {
            createToast('Peringatan', 'Keperluan kedatangan wajib diisi.', 'warning');
            return;
        }

        try {
            const response = await fetch('/api/fo/queues/walkin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nik: nik,
                    name: name,
                    phone: phone,
                    department_id: deptId,
                    next_department_ids: nextDeptIds,
                    purpose: purpose,
                    is_priority: document.getElementById('chkWalkInPriority').checked ? 1 : 0
                })
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                const errMsg = errData.message || `Gagal mencetak tiket walk-in (Status: ${response.status}).`;
                createToast('Gagal Cetak', errMsg, 'warning');
                return;
            }

            const data = await response.json();
            const ticketNum = data.queue_number || data.ticket_code || 'W-000';
            const citizenName = data.visitor_name || data.name || 'Walk-In Citizen';

            const deptSelect = document.getElementById('selWalkInDept');
            const deptName = deptSelect.options[deptSelect.selectedIndex].text;

            foStats.tiketDicetak++;
            document.getElementById('foStatTiket').innerText = foStats.tiketDicetak;

            // Add to live feed with state parameter 'Waiting'
            addLiveFeedRow(citizenName, ticketNum, deptName, 'Walk-In (Tiket Mandiri)', 'Waiting', data.id, data.booking_code, data.purpose);

            createToast('Tiket Dicetak',
                `Tiket ${ticketNum} berhasil dicetak untuk ${citizenName} tujuan ${deptName}.`, 'success');
            resetWalkInForm();
        } catch (error) {
            console.error('Error printing walkin ticket:', error);
            createToast('Koneksi Gagal', 'Tidak dapat terhubung ke server untuk mencetak tiket.', 'warning');
        }
    }

    // Helper functions
    function addLiveFeedRow(name, code, tenant, type, status, id = null, bookingCode = null, serviceName = '-') {
        const tbody = document.getElementById('foLiveFeedBody');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-surface-soft/50 dark:hover:bg-white/5 transition-colors';

        const d = new Date();
        const timeStr = d.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Determine badge style dynamically
        let badgeClass = 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200/50';
        let dotClass = 'bg-amber-500';
        if (status === 'Serving') {
            badgeClass = 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200/50';
            dotClass = 'bg-green-500';
        } else if (status === 'Completed') {
            badgeClass = 'bg-gray-100 dark:bg-gray-800/50 text-gray-700 dark:text-gray-400 border-gray-200/50';
            dotClass = 'bg-gray-500';
        } else if (status === 'Skipped') {
            badgeClass = 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200/50';
            dotClass = 'bg-red-500';
        }

        // Action cell content
        let actionHtml = '<span class="text-muted text-[10px] font-medium">-</span>';
        if (id && (status === 'Waiting' || status === 'Checked-In' || status === 'Booked')) {
            const cancelRoute = `/fo/bookings/${id}/cancel`;
            const displayCode = bookingCode || code;
            const displayName = name.replace(/'/g, "\\'");
            const displayService = serviceName.replace(/'/g, "\\'");
            
            actionHtml = `
                <button type="button"
                    onclick="Alpine.$data(this).openCancelModal('${cancelRoute}', '${displayCode}', '${displayName}', '${displayService}')"
                    class="h-8 px-3.5 bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/20 dark:hover:bg-red-950/40 dark:text-red-400 border border-red-200/60 dark:border-red-900/40 text-[10px] font-bold rounded-pill inline-flex items-center gap-1 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-red-500/20 transition-all cursor-pointer">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </button>
            `;
        }

        tr.innerHTML = `
            <td class="py-3 px-6 font-bold text-ink dark:text-white">${name}</td>
            <td class="py-3 px-4 font-mono font-bold text-primary dark:text-accent-teal">${code}</td>
            <td class="py-3 px-4 font-medium text-muted dark:text-on-dark-soft">${tenant}</td>
            <td class="py-3 px-4 text-muted dark:text-on-dark-soft">${type}</td>
            <td class="py-3 px-4 font-mono text-muted dark:text-on-dark-soft">${timeStr}</td>
            <td class="py-3 px-6">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border ${badgeClass}">
                    <span class="w-1 h-1 rounded-full ${dotClass}"></span>${status}
                </span>
            </td>
            <td class="py-3 px-6 text-right">${actionHtml}</td>
        `;

        // Insert at top of table
        if (tbody.firstChild) {
            tbody.insertBefore(tr, tbody.firstChild);
        } else {
            tbody.appendChild(tr);
        }

        // Limit to 8 rows
        while (tbody.children.length > 8) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    // Toast Alert
    function createToast(title, message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        let borderClr = 'border-green-500';
        let bgClr = 'bg-white dark:bg-gray-800';
        let iconHtml = '';

        if (type === 'success') {
            borderClr = 'border-l-4 border-green-500';
            iconHtml =
                `<svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'warning') {
            borderClr = 'border-l-4 border-amber-500';
            iconHtml =
                `<svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
        } else {
            borderClr = 'border-l-4 border-blue-500';
            iconHtml =
                `<svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }

        toast.className =
            `flex items-start gap-3 p-4 rounded-lg shadow-xl border border-hairline dark:border-white/10 ${bgClr} ${borderClr} max-w-sm pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0`;
        toast.innerHTML = `
            <div class="shrink-0">${iconHtml}</div>
            <div class="flex-grow">
                <h5 class="text-xs font-bold text-ink dark:text-white font-display">${title}</h5>
                <p class="text-[11px] text-muted dark:text-on-dark-soft mt-0.5 font-body leading-tight">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        `;

        container.appendChild(toast);

        // Trigger reflow & animate in
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }
        }, 50);

        // Auto remove after 4s
        setTimeout(() => {
            if (toast.isConnected) {
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(() => {
                    if (toast.isConnected) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 4000);
    }

    // Keep track of shown notification IDs to prevent duplicates in async race conditions
    const shownNotifications = new Set();

    async function pollNotifications() {
        try {
            const response = await fetch('/api/fo/notifications');
            if (!response.ok) return;
            const data = await response.json();
            
            if (data.notifications && data.notifications.length > 0) {
                for (const notification of data.notifications) {
                    if (shownNotifications.has(notification.id)) continue;
                    shownNotifications.add(notification.id);
                    
                    // Show dynamic toast pop-up
                    createToast(notification.title, notification.message, 'info');
                    
                    // Increment stats counter
                    foStats.antreanFO++;
                    const foStatElem = document.getElementById('foStatAntrean');
                    if (foStatElem) {
                        foStatElem.innerText = foStats.antreanFO;
                    }
                    
                    // Mark as read in background
                    fetch(`/api/fo/notifications/${notification.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).catch(err => console.error('Failed to mark notification as read:', err));
                }
            }
            
            // Update the header dot
            const dot = document.getElementById('header-notification-dot');
            if (dot) {
                if (data.unread_count > 0) {
                    dot.classList.remove('hidden');
                } else {
                    dot.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error polling notifications:', error);
        }
    }

    // Start polling immediately and then every 5 seconds
    pollNotifications();
    setInterval(pollNotifications, 5000);
</script>
