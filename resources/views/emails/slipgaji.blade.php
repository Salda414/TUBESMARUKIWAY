<h2>Halo {{ $penggajian->pegawai->nama }},</h2>
<p>Berikut ini adalah slip gaji Anda bulan ini:</p>
<ul>
    <li>Jumlah Hadir: {{ $penggajian->jumlah_hadir }} hari</li>
    <li>Gaji per Hari: Rp{{ number_format($penggajian->gaji_per_hari, 0, ',', '.') }}</li>
    <li>Total Gaji: <strong>Rp{{ number_format($penggajian->total_gaji, 0, ',', '.') }}</strong></li>
</ul>
<p>Slip gaji lengkap terlampir dalam format PDF.</p>
