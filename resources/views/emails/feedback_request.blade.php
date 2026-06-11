<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelayanan Selesai</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f6fa;
            color: #374151;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid #e8ecf4;
        }
        .header {
            background-color: #1B4FA8;
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 32px;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 16px;
        }
        .ticket-info {
            background-color: #f4f6fa;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
            border: 1px solid #d1d9e6;
        }
        .ticket-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-info td {
            padding: 6px 0;
            font-size: 14px;
        }
        .ticket-info td.label {
            color: #6b7280;
            width: 40%;
        }
        .ticket-info td.value {
            font-weight: bold;
            color: #111827;
            text-align: right;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0 16px;
        }
        .btn {
            background-color: #1B4FA8;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 9999px;
            font-weight: bold;
            display: inline-block;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(27, 79, 168, 0.2);
        }
        .footer {
            background-color: #101826;
            color: #a8b4c8;
            padding: 24px;
            text-align: center;
            font-size: 12px;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mal Pelayanan Publik</h1>
            <p style="color: #29ABE2; margin: 4px 0 0; font-weight: bold; font-size: 14px;">KOTA SAWAHLUNTO</p>
        </div>
        <div class="content">
            <p>Halo, <strong>{{ $queue->user ? $queue->user->name : 'Pengunjung' }}</strong>.</p>
            <p>Terima kasih telah berkunjung ke Mal Pelayanan Publik Kota Sawahlunto. Pelayanan Anda pada hari ini telah selesai diproses.</p>
            
            <div class="ticket-info">
                <table>
                    <tr>
                        <td class="label">Nomor Antrean</td>
                        <td class="value" style="font-family: monospace; font-size: 18px; color: #1B4FA8;">{{ $queue->queue_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Loket Pelayanan</td>
                        <td class="value">Loket {{ $queue->department ? $queue->department->nomor_loket : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Instansi</td>
                        <td class="value">{{ $queue->department ? $queue->department->name : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Layanan</td>
                        <td class="value">{{ $queue->purpose ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Kunjungan</td>
                        <td class="value">{{ $queue->booking_date ? $queue->booking_date->format('d M Y') : '-' }}</td>
                    </tr>
                </table>
            </div>

            <p>Kami senantiasa berupaya meningkatkan kualitas layanan kami. Silakan luangkan waktu sejenak untuk memberikan ulasan (rating & komentar) terhadap pelayanan yang Anda terima hari ini.</p>
            
            <div class="btn-container">
                <a href="{{ route('dashboard') }}" class="btn">Berikan Ulasan Sekarang</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>Mal Pelayanan Publik Kota Sawahlunto</strong></p>
            <p>Sawahlunto, Kota Wisata Tambang yang Berbudaya</p>
            <p style="margin-top: 12px; opacity: 0.6;">Email ini dikirim secara otomatis oleh sistem antrean. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
