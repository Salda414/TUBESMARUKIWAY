<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Produk; //untuk akses kelas model produk

class KeranjangController extends Controller
{
    public function daftarproduk()
    {
        

        // ambil data produk
        $Produk = Produk::all();
        // kirim ke halaman view
        return view('galeri',
                        [ 
                            'produk'=>$Produk,
                        ]
                    ); 
    }
}
