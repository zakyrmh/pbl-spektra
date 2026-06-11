<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Booking Antrean MPP Sawahlunto</title>
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
            background-color: #1B4FA8;
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
            opacity: 0.8;
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
            border-left: 4px solid #29ABE2;
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
            font-size: 20px;
            color: #1B4FA8;
            font-family: monospace;
        }
        .instructions {
            background-color: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 8px;
            padding: 16px;
            font-size: 13px;
            color: #92400E;
        }
        .instructions ul {
            margin: 5px 0 0 0;
            padding-left: 20px;
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
            <h1>Mal Pelayanan Publik</h1>
            <p>Kota Sawahlunto</p>
        </div>
        <div class="content">
            <h2>Halo, {{ $booking->user->name }}</h2>
            <p>Terima kasih telah menggunakan layanan antrean digital mandiri. Reservasi antrean Anda telah berhasil kami catat dengan rincian sebagai berikut:</p>

            <div class="ticket-box">
                <div class="ticket-row">
                    <span class="label">Layanan</span>
                    <span class="value">{{ $booking->purpose }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Instansi</span>
                    <span class="value">{{ $booking->department->name }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Tanggal Kunjungan</span>
                    <span class="value">{{ $booking->booking_date->translatedFormat('d F Y') }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Sesi Waktu</span>
                    <span class="value">{{ $booking->session_name ?? 'Harian' }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">Kode Booking</span>
                    <span class="value booking-code">{{ $booking->booking_code }}</span>
                </div>
            </div>

            <div class="instructions">
                <strong>PENTING: Langkah Selanjutnya di Loket:</strong>
                <ul>
                    <li>Setibanya di Mal Pelayanan Publik (MPP), tunjukkan Kode Booking atau QR Code di atas pada petugas <strong>Front Office (FO)</strong>.</li>
                    <li>Petugas FO akan melakukan verifikasi fisik kartu identitas dan menerbitkan nomor antrean aktif Anda.</li>
                    <li>Mohon hadir 15 menit sebelum sesi kunjungan Anda dimulai.</li>
                </ul>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MPP Kota Sawahlunto. All rights reserved.</p>
            <p>Sawahlunto, Kota Wisata Tambang yang Berbudaya</p>
        </div>
    </div>
</body>
</html>
