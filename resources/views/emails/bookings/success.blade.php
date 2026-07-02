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
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Nama Warga</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ $booking->user->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Nomor Antrean</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">
                            @if($booking->queue_number)
                                <strong style="color: #1B4FA8; font-size: 16px;">{{ $booking->queue_number }}</strong>
                            @else
                                <span style="color: #92400E; font-style: italic;">Belum Check-In</span>
                            @endif
                        </td>
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
                        <td style="padding: 6px 0; color: #6B7280; font-weight: 600; font-size: 14px; border-bottom: 1px solid #E5E7EB;">Sesi Kunjungan</td>
                        <td style="padding: 6px 0; color: #111827; font-weight: 700; font-size: 14px; text-align: right; border-bottom: 1px solid #E5E7EB;">{{ $booking->session_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 0 6px 0; color: #6B7280; font-weight: 600; font-size: 14px;">Kode Booking</td>
                        <td style="padding: 12px 0 6px 0; color: #1B4FA8; font-weight: 700; font-size: 18px; text-align: right; font-family: monospace; letter-spacing: 1px;">{{ $booking->booking_code }}</td>
                    </tr>
                </table>
            </div>

            {{-- QR Code Area --}}
            <div style="text-align: center; margin: 25px 0;">
                <div style="display: inline-block; background-color: #FFFFFF; border: 1px solid #D1D9E6; padding: 15px; border-radius: 8px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($booking->booking_code) }}" 
                         alt="QR Code Booking" 
                         width="160" 
                         height="160" 
                         style="display: block; margin: 0 auto; border: none;">
                </div>
                <p style="font-size: 12px; color: #6B7280; margin-top: 10px; margin-bottom: 0;">Scan QR Code ini pada mesin kiosk/stasiun check-in di MPP</p>
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
