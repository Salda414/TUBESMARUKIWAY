<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pembayaran</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Detail Pembayaran</h4>
            </div>
            <div class="card-body">
                <p><strong>Kode Faktur:</strong> {{ $penjualan->kode_faktur }}</p>
                <p><strong>Nama Pembeli:</strong> {{ $penjualan->pembeli->nama_pembeli }}</p>
                <p><strong>Total Tagihan:</strong> <span class="badge bg-success">Rp. {{ number_format($penjualan->tagihan, 0, ',', '.') }}</span></p>
                <p><strong>Jumlah Item:</strong> {{ $jumlah_item }}</p>

                <h5 class="mt-4">Detail Barang yang Dibeli</h5>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Harga Barang</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail_tagihan as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>Rp. {{ number_format($item->harga_barang, 0, ',', '.') }}</td>
                            <td>{{ $item->jumlah }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Tombol Pembayaran -->
                <div class="text-center mt-4">
                    <button id="pay-button">Bayar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (CDN) -->
    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
    // Pastikan Midtrans Snap.js sudah dimuat
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        window.snap.pay('{{$snap_token}}', {
        onSuccess: function(result){
            console.log('Pembayaran berhasil:', result);
            window.location.href = "/pembayaran/autorefresh/";
        },
        onPending: function(result){
            console.log('Pembayaran tertunda:', result);
            window.location.href = "/pembayaran/autorefresh/";
        },
        onError: function(result){
            console.log('Pembayaran gagal:', result);
            alert("Pembayaran gagal. Silakan coba lagi.");
        },
        onClose: function(){
            alert("Anda menutup pop-up pembayaran sebelum menyelesaikan transaksi.");
        }
        });
    });
    </script>

</body>
</html>
