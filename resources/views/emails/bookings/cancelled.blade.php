<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pembatalan Booking Antrean MPP Sawahlunto</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #F4F6FA;
            color: #374151;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #FFFFFF;
            border-radius: 12px;
            border: 1px solid #D1D9E6;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #DC2626;
            color: #FFFFFF;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
        }
        .content h2 {
            font-size: 18px;
            color: #111827;
            margin-top: 0;
        }
        .ticket-box {
            background-color: #EFF2F7;
            border: 1px solid #DDE3EE;
            border-left: 4px solid #DC2626;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .ticket-row:last-child {
            margin-bottom: 0;
            border-top: 1px dashed #D1D9E6;
            padding-top: 12px;
            margin-top: 12px;
        }
        .label {
            color: #6B7280;
            font-weight: 600;
        }
        .value {
            color: #111827;
            font-weight: 700;
            text-align: right;
        }
        .booking-code {
            font-size: 16px;
            color: #374151;
            font-family: monospace;
        }
        .reason-box {
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-radius: 8px;
            padding: 16px;
            font-size: 13px;
            color: #991B1B;
            margin-top: 20px;
        }
        .footer {
            background-color: #101826;
            color: #A8B4C8;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pembatalan Reservasi Antrean</h1>
            <p>Mal Pelayanan Publik Kota Sawahlunto</p>
        </div>
        <div class="content">
            <h2>Halo, {{ $booking->user->name }}</h2>
            <p>Kami ingin menginformasikan bahwa reservasi antrean digital Anda telah dibatalkan dengan rincian pelayanan sebagai berikut:</p>

            <div class="ticket-box">
                <div class="ticket-row">
                    <span class="label">Layanan</span>
                    <span class="value">{{ $booking->service->name }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Instansi</span>
                    <span class="value">{{ $booking->service->department->name }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Rencana Tanggal Kunjungan</span>
                    <span class="value">{{ $booking->booking_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Sesi Waktu</span>
                    <span class="value">{{ $booking->schedule?->session_name ?? 'Harian' }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Kode Booking</span>
                    <span class="value booking-code">{{ $booking->booking_code }}</span>
                </div>
            </div>

            <div class="reason-box">
                <strong>Alasan Pembatalan:</strong>
                <p style="margin: 6px 0 0 0; font-style: italic;">"{{ $booking->cancel_reason }}"</p>
            </div>

            <p style="margin-top: 24px; font-size: 13px; color: #6B7280;">Jika Anda masih memerlukan layanan ini, silakan melakukan pendaftaran ulang dengan memilih sesi waktu lain yang tersedia melalui aplikasi antrean digital mandiri kami.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MPP Kota Sawahlunto. All rights reserved.</p>
            <p>Sawahlunto, Kota Wisata Tambang yang Berbudaya</p>
        </div>
    </div>
</body>
</html>
