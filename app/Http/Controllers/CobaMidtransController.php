<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CobaMidtransController extends Controller
{
    public function cekmidtrans(Request $request)
    {
        // definisikan parameter midtrans
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
        // Optional
        $item1_details = array(
            'id' => 'a1',
            'price' => 10000,
            'quantity' => 3,
            'name' => "Apple"
        );

        // Optional
        $item2_details = array(
            'id' => 'a2',
            'price' => 20000,
            'quantity' => 1,
            'name' => "Orange"
        );

        // Optional
        $item_details = array ($item1_details, $item2_details);

        // Optional, remove this to display all available payment methods
        $enable_payments = array("bca_va","bni_va");

        $params = array(
            'transaction_details' => array(
                'order_id' => rand(), //idpesanan ini nanti dpt diambil dari no_pesanan
                'gross_amount' => 50000,
            ),
            'customer_details' => array(
                'first_name' => 'Tina',
                'last_name' => 'Toon',
                'email' => 'nikita@gmail.com',
                'phone' => '0821142334',
            ),
            'item_details' => $item_details,
            'enabled_payments' => $enable_payments,
        );
         
        $snapToken = \Midtrans\Snap::getSnapToken($params);
        // dd($snapToken);
        return view(    'midtrans.viewsampel',
                        [
                            'snap_token' => $snapToken,
                        ]
        );
    }
}
