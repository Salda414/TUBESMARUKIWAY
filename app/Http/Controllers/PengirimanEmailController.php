<?php

namespace App\Http\Controllers;

use App\Mail\SlipGajiMail;
use App\Models\Penggajian;
use App\Models\Pengirimanemail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class PengirimanEmailController extends Controller
{
    public function kirim($id)
    {
        $penggajian = Penggajian::with('pegawai')->findOrFail($id);

        Mail::to($penggajian->pegawai->email)->send(new SlipGajiMail($penggajian));

        Pengirimanemail::create([
            'penggajians_id' => $penggajian->id,
            'status' => 'terkirim',
            'tgl_pengiriman_pesan' => Carbon::now(),
        ]);

        return back()->with('success', 'Slip gaji berhasil dikirim ke email pegawai!');
    }
}
