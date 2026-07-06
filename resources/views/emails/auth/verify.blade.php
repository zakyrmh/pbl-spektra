<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Alamat Email Anda</title>
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
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-verify {
            display: inline-block;
            background-color: #29ABE2;
            color: #FFFFFF;
            text-decoration: none;
            padding: 12px 30px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 30px;
            box-shadow: 0 4px 6px rgba(41, 171, 226, 0.2);
            transition: background-color 0.2s;
        }
        .btn-verify:hover {
            background-color: #1B98D1;
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
            <h2>Halo, {{ $name }}</h2>
            <p>Terima kasih telah mendaftar di <strong>Sistem Antrean Digital MPP Kota Sawahlunto</strong>.</p>
            <p>Untuk mengaktifkan akun Anda dan mulai menggunakan layanan booking antrean mandiri kami, silakan verifikasi alamat email Anda dengan mengeklik tombol di bawah ini:</p>

            <div class="btn-container">
                <!-- Inline style color to guarantee white text on blue button across clients -->
                <a href="{{ $url }}" class="btn-verify" style="color: #ffffff; text-decoration: none;">Verifikasi Alamat Email</a>
            </div>

            <p>Jika tombol di atas tidak berfungsi, Anda juga dapat menyalin dan menempelkan tautan berikut ke browser Anda:</p>
            <p style="word-break: break-all; font-size: 12px; color: #1B4FA8;"><a href="{{ $url }}">{{ $url }}</a></p>

            <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 30px 0;">
            <p style="font-size: 12px; color: #6B7280; margin-bottom: 0;">Jika Anda merasa tidak pernah melakukan pendaftaran akun ini, silakan abaikan email ini.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MPP Kota Sawahlunto. All rights reserved.</p>
            <p>Sawahlunto, Kota Wisata Tambang yang Berbudaya</p>
        </div>
    </div>
</body>
</html>
