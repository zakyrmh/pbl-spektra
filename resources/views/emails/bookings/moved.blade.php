<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemindahan Sesi Booking Antrean MPP Sawahlunto</title>
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
            background-color: #F59E0B;
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
            border-left: 4px solid #F59E0B;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
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
            <h1>Pemindahan Sesi Antrean</h1>
            <p>Mal Pelayanan Publik Kota Sawahlunto</p>
        </div>
        <div class="content">
            <h2>Halo, {{ $booking->user->name }}</h2>
            <p>Kami menginformasikan bahwa Anda belum melakukan check-in pada **Sesi 1** (pagi) hari ini untuk reservasi antrean digital Anda.</p>
            <p>Karena kuota **Sesi 2** (siang) hari ini masih tersedia, sistem kami telah otomatis memindahkan sesi kunjungan Anda dengan rincian berikut:</p>

            <div class="ticket-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Nama Warga</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ $booking->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Instansi Tujuan</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ $booking->department->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Layanan / Keperluan</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ $booking->purpose }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Tanggal Kunjungan</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ $booking->booking_date->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Sesi Sebelumnya</td>
                        <td style="padding: 6px 0; color: #DC2626; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB; text-decoration: line-through;">Sesi 1</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Sesi Baru (Sekarang)</td>
                        <td style="padding: 6px 0; color: #15803D; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">Sesi 2</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 0 6px 0; color: #6B7280; font-weight: 600; font-size: 14px;">Kode Booking</td>
                        <td style="padding: 12px 0 6px 0; color: #1B4FA8; font-weight: 700; font-size: 18px; text-align: right; font-family: monospace; letter-spacing: 1px;">{{ $booking->booking_code }}</td>
                    </tr>
                </table>
            </div>

            <div class="instructions">
                <strong>Langkah Selanjutnya:</strong>
                <ul>
                    <li>Anda masih dapat melakukan check-in menggunakan Kode Booking atau QR Code lama Anda.</li>
                    <li>Silakan datang ke loket Front Office (FO) sebelum pukul **15:00** hari ini untuk melakukan verifikasi kedatangan.</li>
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
