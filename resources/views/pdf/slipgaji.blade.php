<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px 0; }
        .footer {
            margin-top: 40px;
            font-style: italic;
            text-align: center;
            color: #555;
        }
    </style>
</head>
<body>
    <h2>Slip Gaji Pegawai</h2>
    <table>
        <tr><td><strong>Nama Pegawai</strong></td><td>: {{ $penggajian->pegawai->nama }}</td></tr>
        <tr><td><strong>Jumlah Hadir</strong></td><td>: {{ $penggajian->jumlah_hadir }}</td></tr>
        <tr><td><strong>Gaji / Hari</strong></td><td>: Rp{{ number_format($penggajian->gaji_per_hari, 0, ',', '.') }}</td></tr>
        <tr><td><strong>Total Gaji</strong></td><td>: <strong>Rp{{ number_format($penggajian->total_gaji, 0, ',', '.') }}</strong></td></tr>
        <tr><td><strong>Periode</strong></td><td>: {{ $penggajian->periode_awal }} s/d {{ $penggajian->periode_akhir }}</td></tr>
    </table>

    <div class="footer">
        <p>Terima kasih atas dedikasi dan kerja keras Anda selama periode ini.</p>
        <p>Semoga terus semangat dalam memberikan kontribusi terbaik bagi perusahaan.</p>
        <p>Salam hangat,</p>
        <p><strong>Tim HRD - MirukiWay</strong></p>
    </div>
</body>
</html>
 