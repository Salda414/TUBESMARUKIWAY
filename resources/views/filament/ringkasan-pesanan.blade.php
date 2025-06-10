<div>
    <h3 class="text-lg font-semibold mb-2">Detail Faktur: {{ $no_faktur }}</h3>
    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tgl)->format('d M Y H:i') }}</p>
    <p><strong>Pelanggan:</strong> {{ $nama_pelanggan }}</p>

    <h4 class="mt-4 font-semibold">Item Dibeli:</h4>

    @if (empty($items))
        <p>Belum ada item yang dipilih.</p>
    @else
        <table class="table-auto w-full mt-2 border border-gray-300">
            <thead>
                <tr>
                    <th class="border px-2 py-1 text-left">Produk</th>
                    <th class="border px-2 py-1 text-right">Jumlah</th>
                    <th class="border px-2 py-1 text-right">Harga</th>
                    <th class="border px-2 py-1 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php
                        $produk = \App\Models\Produk::find($item['produk_id'] ?? null);
                        $nama = $produk?->nama_produk ?? 'Tidak diketahui';
                        $jumlah = $item['jumlah'] ?? 0;
                        $harga = $item['harga'] ?? 0;
                        $subtotal = $jumlah * $harga;
                    @endphp
                    <tr>
                        <td class="border px-2 py-1">{{ $nama }}</td>
                        <td class="border px-2 py-1 text-right">{{ $jumlah }}</td>
                        <td class="border px-2 py-1 text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h4 class="mt-4 font-semibold">Total Tagihan: Rp {{ number_format($tagihan ?? 0, 0, ',', '.') }}</h4>
</div>
