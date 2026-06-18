<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Antrean MPP Kota Sawahlunto</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1B4FA8;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #1B4FA8;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
            color: #555555;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .meta-title {
            font-weight: bold;
            width: 100px;
        }
        .meta-value {
            color: #444444;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: #1B4FA8;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            width: 33.33%;
            padding: 4px 0;
        }
        .summary-label {
            font-size: 9px;
            color: #666666;
            text-transform: uppercase;
        }
        .summary-val {
            font-size: 14px;
            font-weight: bold;
            color: #222222;
            margin-top: 1px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .data-table th {
            background-color: #1B4FA8;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            border: 1px solid #1B4FA8;
            font-size: 9px;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f1f5f9;
        }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 8px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mal Pelayanan Publik Kota Sawahlunto</h1>
        <p>Laporan Kinerja & Rekapitulasi Pelayanan Antrean Digital</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-title">Nama Laporan:</td>
            <td class="meta-value">{{ $report->title }}</td>
            <td class="meta-title" style="text-align: right;">Dicetak Pada:</td>
            <td class="meta-value" style="text-align: right;">{{ now()->format('d-m-Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-title">Periode Rekap:</td>
            <td class="meta-value">{{ $report->start_date->format('d M Y') }} s/d {{ $report->end_date->format('d M Y') }}</td>
            <td class="meta-title" style="text-align: right;">Petugas FO:</td>
            <td class="meta-value" style="text-align: right;">{{ $report->creator?->name ?? 'Front Office' }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <h3>Ringkasan Kinerja</h3>
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-label">Total Antrean</div>
                    <div class="summary-val">{{ number_format($report->data_summary['total_visitors'] ?? 0) }}</div>
                </td>
                <td>
                    <div class="summary-label">Selesai Dilayani</div>
                    <div class="summary-val">{{ number_format($report->data_summary['completed_count'] ?? 0) }}</div>
                </td>
                <td>
                    <div class="summary-label">Tingkat Kehadiran</div>
                    <div class="summary-val">{{ $report->data_summary['attendance_rate'] ?? 0 }}%</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="summary-label">Lewati / Skipped</div>
                    <div class="summary-val">{{ number_format($report->data_summary['skipped_count'] ?? 0) }}</div>
                </td>
                <td>
                    <div class="summary-label">Rata-Rata Pelayanan</div>
                    <div class="summary-val">{{ $report->data_summary['avg_service_time'] ?? 0 }} mnt</div>
                </td>
                <td>
                    <div class="summary-label">Rata-Rata Tunggu</div>
                    <div class="summary-val">{{ $report->data_summary['avg_waiting_time'] ?? 0 }} mnt</div>
                </td>
            </tr>
        </table>
    </div>

    <h3>Rincian Riwayat Antrean (Selesai)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 60px;">No. Antrean</th>
                <th style="width: 85px;">NIK</th>
                <th>Nama Lengkap</th>
                <th>Instansi/Gerai</th>
                <th>Layanan</th>
                <th style="width: 50px;">Panggil</th>
                <th style="width: 50px;">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($queues as $index => $q)
                @php
                    $name = $q->user?->name ?? '-';
                    $nik = $q->user?->nik ?? '-';
                    $deptName = $q->department?->name ?? '-';
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $q->booking_date instanceof \Carbon\Carbon ? $q->booking_date->format('d-m-Y') : (string) $q->booking_date }}</td>
                    <td style="font-weight: bold; color: #1B4FA8;">{{ $q->queue_number }}</td>
                    <td>{{ $nik }}</td>
                    <td style="font-weight: bold;">{{ $name }}</td>
                    <td>{{ $deptName }}</td>
                    <td>{{ $q->purpose ?? '-' }}</td>
                    <td>{{ $q->called_at ? \Carbon\Carbon::parse($q->called_at)->format('H:i:s') : '—' }}</td>
                    <td>{{ $q->completed_at ? \Carbon\Carbon::parse($q->completed_at)->format('H:i:s') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #888888;">
                        Tidak ada data transaksi antrean untuk laporan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini diterbitkan secara elektronik oleh Sistem Antrian Digital Mal Pelayanan Publik Kota Sawahlunto.
    </div>
</body>
</html>
