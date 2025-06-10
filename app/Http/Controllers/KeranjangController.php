<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk; //untuk akses kelas model barang
use App\Models\Penjualan; //untuk akses kelas model penjualan
use App\Models\PenjualanBarang; //untuk akses kelas model penjualan
use App\Models\Pembayaran; //untuk akses kelas model pembayaran
use App\Models\Pelanggan; //untuk akses kelas model pelanggan
use Illuminate\Support\Facades\DB; //untuk menggunakan db
use Illuminate\Support\Facades\Auth; //agar bisa mengakses session user_id dari user yang login
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//use App\Models\Produk; //untuk akses kelas model produk

class KeranjangController extends Controller
{
    public function daftarproduk()
    {
        $id_user = Auth::user()->id;

        // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
        $pelanggan = Pelanggan::where('user_id', $id_user)
                        ->select(DB::raw('id'))
                        ->first();
        $id_pelanggan = $pelanggan->id;

        // ambil data produk
        $produk = Produk::all();

        // query total belanja yang belum terbayar
        $produkdibeli = Penjualan::where('pelanggan_id', $id_pelanggan)
                         //->where('pembayaran', 0)
                         ->first();
        // $produkdibeli = DB::table('penjualan')
        //                 ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
        //                 ->where('penjualan.pelanggan_id', '=', $id_pelanggan) 
        //                 ->where(function($query) {
        //                     $query->where('pembayaran.gross_amount', 0)
        //                           ->orWhere(function($q) {
        //                               $q->where('pembayaran.status_code', '!=', 200)
        //                                 ->where('pembayaran.jenis_pembayaran', 'pg');
        //                           });
        //                 })
        //                 ->selectRaw('IFNULL(COUNT(penjualan.tagihan), 0) as tagihan')
        //                 ->value('tagihan');

        // dd(var_dump($produkdibeli));
        // jumlah produk dibeli
        $jmlprodukdibeli = DB::table('penjualan')
                            ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
                            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                            ->select(DB::raw('COUNT(DISTINCT produk_id) as total'))
                            ->where('penjualan.pelanggan_id', '=', $id_pelanggan) 
                            ->where(function($query) {
                                $query->where('pembayaran.gross_amount', 0)
                                      ->orWhere(function($q) {
                                          $q->where('pembayaran.status_code', '!=', 200)
                                            ->where('pembayaran.jenis_pembayaran', 'pg');
                                      });
                            })
                            ->get();

        $t = DB::table('penjualan')
        ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
        ->select(DB::raw('SUM(harga_jual * jumlah) as total'))
        ->where('penjualan.pelanggan_id', '=', $id_pelanggan) 
        ->where(function($query) {
            $query->where('pembayaran.gross_amount', 0)
                  ->orWhere(function($q) {
                      $q->where('pembayaran.status_code', '!=', 200)
                        ->where('pembayaran.jenis_pembayaran', 'pg');
                  });
        })
        ->first();

        // kirim ke halaman view
        return view('galeri',
                        [ 
                            'produk'=>$produk,
                            'total_belanja' => $t->total ?? 0,
                            'jmlbarangdibeli' => $jmlprodukdibeli[0]->total ?? 0
                        ]
                    ); 
    }

    // halaman tambah keranjang
    public function tambahKeranjang(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:produk,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $id_user = Auth::user()->id;

            // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
            $pelanggan = Pelanggan::where('user_id', $id_user)
                            ->select(DB::raw('id'))
                            ->first();
            $id_pelanggan = $pelanggan->id;

            Log::info('Product ID: ' . $request->product_id);
            Log::info('Quantity: ' . $request->quantity);
            Log::info('Pelanggan ID: ' . $id_pelanggan);
            Log::info('Penjualan Exist: ' . json_encode($penjualanExist));

            // cek di database apakah ada nomor faktur yang masih aktif
            // dilihat dari pembayaran yg masih 0
            
            try{
                $product = Produk::find($request->product_id); //ambi data produk simpan di tabel product
                if (!$product) {
                    return response()->json(['success' => false, 'message' => 'produk tidak ditemukan!']);
                }
                $harga = $product->harga_produk;
                $jumlah = (int) $request->quantity;
                $produk_id = $request->product_id;

               // Cek apakah ada penjualan dengan gross_amount = 0
                $penjualanExist = DB::table('penjualan')
                ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
                ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                ->where('penjualan.pelanggan_id', $id_pelanggan)
                ->where(function($query) {
                    $query->where('pembayaran.gross_amount', 0)
                          ->orWhere(function($q) {
                              $q->where('pembayaran.status_code', '!=', 200)
                                ->where('pembayaran.jenis_pembayaran', 'pg');
                          });
                })
                ->select('penjualan.id') // Ambil ID saja untuk dicek
                ->first();

                if (!$penjualanExist) {
                    // Buat penjualan baru jika tidak ada
                    $penjualan = Penjualan::create([
                        'no_faktur'   => Penjualan::getKodeFaktur(),
                        'tgl'         => now(),
                        'pelanggan_id'  => $id_pelanggan,
                        'tagihan'     => 0,
                        'status'      => 'pesan',
                    ]);

                    // Buat pembayaran baru
                    $pembayaran = Pembayaran::create([
                        'penjualan_id'      => $penjualan->id,
                        'tgl_bayar'         => now(),
                        'jenis_pembayaran'  => 'pg',
                        'gross_amount'      => 0,
                    ]);
                }else{
                    $penjualan = Penjualan::find($penjualanExist->id);
                }

                // Tambahkan produk ke penjualan_barang
                Penjualanproduk::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $produk_id,
                    'harga_beli'=>$harga,
                    'harga_jual'=>$harga*1.2,
                    'jumlah' => $jumlah,
                    'tgl'=>date('Y-m-d')
                ]);

                // Update total tagihan pada tabel penjualan
                // $penjualan->tagihan = Penjualanproduk::where('penjualan_id', $penjualan->id)->sum('total');
                $tagihan = DB::table('penjualan')
                ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
                ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                ->select(DB::raw('SUM(harga_jual * jumlah) as total'))
                ->where('penjualan.pelanggan_id', '=', $id_pelanggan) 
                ->where(function($query) {
                    $query->where('pembayaran.gross_amount', 0)
                          ->orWhere(function($q) {
                              $q->where('pembayaran.status_code', '!=', 200)
                                ->where('pembayaran.jenis_pembayaran', 'pg');
                          });
                })
                ->first();
                $penjualan->tagihan = $tagihan->total;
                $penjualan->save();

                // update stok produk kurangi 1
                Produk::where('id', $produk_id)->decrement('stok', $jumlah);

                // hitung total produk
                $jmlprodukdibeli = DB::table('penjualan')
                            ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
                            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                            ->select(DB::raw('COUNT(DISTINCT produk_id) as total'))
                            ->where('penjualan.pelanggan_id', '=', $id_pelanggan) 
                            ->where(function($query) {
                                $query->where('pembayaran.gross_amount', 0)
                                      ->orWhere(function($q) {
                                          $q->where('pembayaran.status_code', '!=', 200)
                                            ->where('pembayaran.jenis_pembayaran', 'pg');
                                      });
                            })
                            ->get();

                // DB::commit(); //commit ke database
                return response()->json(['success' => true, 'message' => 'Transaksi berhasil ditambahkan!', 
                'total' => $penjualan->tagihan, 'jmlprodukdibeli'=>$jmlprodukdibeli[0]->total ?? 0]);

            }catch(\Exception $e){
                // DB::rollBack(); //rollback jika ada error
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    // halaman lihat keranjang
    public function lihatkeranjang(){
        date_default_timezone_set('Asia/Jakarta');
        $id_user = Auth::user()->id;

        // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
        $pelanggan = Pelanggan::where('user_id', $id_user)
                        ->select(DB::raw('id'))
                        ->first();
        $id_pelanggan = $pelanggan->id;
        // dd(var_dump($id_pelanggan));

        $produk = DB::table('penjualan')
                        ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
                        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                        ->join('produk', 'penjualan_barang.produk_id', '=', 'produk.id')
                        ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                        ->select('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan', 'penjualan_barang.produk_id', 'produk.nama_produk','penjualan_barang.harga_jual', 
                                 'produk.gambar','pembayaran.order_id',
                                  DB::raw('SUM(penjualan_barang.jumlah) as total_produk'),
                                  DB::raw('SUM(penjualan_barang.harga_jual * penjualan_barang.jumlah) as total_belanja'))
                        ->where('penjualan.pelanggan_id', '=',$id_pelanggan) 
                        ->where(function($query) {
                            $query->where('pembayaran.gross_amount', 0)
                                  ->orWhere(function($q) {
                                      $q->where('pembayaran.status_code', '!=', 200)
                                        ->where('pembayaran.jenis_pembayaran', 'pg');
                                  });
                        })
                        ->groupBy('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan','penjualan_barang.produk_id', 'produk.nama_produk','penjualan_barang.harga_jual',
                                  'produk.gambar','pembayaran.order_id',
                                 )
                        ->get();

        // hitung jumlah total tagihan
        $ttl = 0; $jumlah_brg = 0; $kode_faktur = '';
        foreach($produk as $p){
            $ttl += $p->total_belanja;
            $jumlah_brg += 1;
            $kode_faktur = $p->no_faktur;
            $idpenjualan = $p->id;
            $odid = $p->order_id;
        }

        // cek dulu apakah sudah ada di midtrans dan belum expired
        $ch = curl_init(); 
        $login = env('MIDTRANS_SERVER_KEY');
        $password = '';
        if(isset($odid)){
            $parts = explode('-', $odid);
            $substring = $parts[0] . '-' . $parts[1];
            $orderid = $substring;
        }else{
            $orderid =$kode_faktur.'-'.date('YmdHis'); //FORMAT
        }

        $URL =  'https://api.sandbox.midtrans.com/v2/'.$orderid.'/status';
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");  
        $output = curl_exec($ch); 
        curl_close($ch);    
        $outputjson = json_decode($output, true); //parsing json dalam bentuk assosiative array
        // return $outputjson;

        // ambil statusnya
        if($outputjson['status_code']==404 or in_array($outputjson['transaction_status'], ['expire', 'cancel', 'deny'])){
            // echo "transaksi tidak ditemukan diserver midtrans ";
            // cek jika jumlah datanya 0 maka jangan menjalankan payment gateway
            if($ttl>0){
                // proses generate token payment gateway
                $order_id = $kode_faktur.'-'.date('YmdHis');
                

                $myArray = array(); //untuk menyimpan objek array
                $i = 1;
                foreach($produk as $k):
                    // untuk data item detail
                    // kita perlu membuat objek dulu kemudian di masukkan ke array
                    $foo = array(
                            'id'=> $i,
                            'price' => $k->harga_jual,
                            'quantity' => $k->total_produk,
                            'name' => $k->nama_produk,

                    );
                    $i++;
                    // tambahkan ke myarray
                    array_push($myArray,$foo);
                endforeach;
                
                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                \Midtrans\Config::$isProduction = false;
                // Set sanitization on (default)
                \Midtrans\Config::$isSanitized = true;
                // Set 3DS transaction for credit card to true
                \Midtrans\Config::$is3ds = true;

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order_id, 
                        'gross_amount' => $ttl, //gross amount diisi total tagihan
                    ),
                    'item_details' => $myArray,
                    'expiry' => [
                            'start_time' => date("Y-m-d H:i:s O"), // sekarang
                            'unit' => 'minutes', // bisa 'minutes', 'hours', atau 'days'
                            'duration' => 2 // expired dalam 60 menit
                    ]
                );

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                $pembayaran = Pembayaran::updateOrCreate(
                    ['penjualan_id' => $idpenjualan], // Cek apakah id penjualan sudah ada
                    [
                        'tgl_bayar'        => now(),
                        'jenis_pembayaran' => 'pg', // Payment Gateway
                        'order_id'         => $order_id,
                        'gross_amount'     => $ttl,
                        'status_code'      => '201', // 201 = Pending
                        'status_message'   => 'Pending payment', // Status awal
                        'transaction_id' => $snapToken, //snap tokennya di simpan di transaction id
                    
                    ]
                );

                return view( 'keranjang',
                            [
                                'produk' => $produk,
                                'total_tagihan' => $ttl,
                                'jumlah_brg' => $jumlah_brg,
                                'snap_token' => $snapToken,
                            ]
                );
            }else{
                // kalau transaksi kosong diarahkan saja ke depan
                return redirect('/depan');
            }
        }else{
            // echo "transaksi ditemukan diserver midtrans, maka tinggal bayar";

            $tagihan = DB::table('penjualan')
            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
            ->select(DB::raw('transaction_id'))
            ->where('penjualan.pelanggan_id', '=', $id_pelanggan) 
            ->where(function($query) {
                $query->where('pembayaran.gross_amount', 0)
                      ->orWhere(function($q) {
                          $q->where('pembayaran.status_code', '!=', 200)
                            ->where('pembayaran.jenis_pembayaran', 'pg');
                      });
            })
            ->first();

            $produk = DB::table('penjualan')
                        ->join('penjualan_barang', 'penjualan.id', '=', 'penjualan_barang.penjualan_id')
                        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                        ->join('produk', 'penjualan_barang.produk_id', '=', 'produk.id')
                        ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                        ->select('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan', 'penjualan_barang.produk_id', 'produk.nama_produk','penjualan_barang.harga_jual', 
                                 'produk.gambar',
                                  DB::raw('SUM(penjualan_barang.jumlah) as total_produk'),
                                  DB::raw('SUM(penjualan_barang.harga_jual * penjualan_barang.jumlah) as total_belanja'))
                        ->where('penjualan.pelanggan_id', '=',$id_pelanggan) 
                        ->where(function($query) {
                            $query->where('pembayaran.gross_amount', 0)
                                  ->orWhere(function($q) {
                                      $q->where('pembayaran.status_code', '!=', 200)
                                        ->where('pembayaran.jenis_pembayaran', 'pg');
                                  });
                        })
                        ->groupBy('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan','penjualan_barang.produk_id', 'produk.nama_produk','penjualan_barang.harga_jual',
                                  'produk.gambar',
                                 )
                        ->get();

            $ttl = 0; $jumlah_brg = 0; $kode_faktur = '';
            foreach($produk as $p){
                $ttl += $p->total_belanja;
                $jumlah_brg += 1;
                $kode_faktur = $p->no_faktur;
                $idpenjualan = $p->id;
            }

            return view('keranjang', [
                'produk' => $produk,
                'total_tagihan' => $ttl,
                'jumlah_brg' => $jumlah_brg,
                'snap_token' => $tagihan->transaction_id
            ]);
        }

        
    }
        

        // ambil data produk
        //$produk = Produk::all();
        // kirim ke halaman view
        // return view('galeri',
        //                 [ 
        //                     'produk'=>$produk,
        //                 ]
        //             ); 
    //}
}
