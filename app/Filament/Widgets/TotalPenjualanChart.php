<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PenjualanBarang;

class TotalPenjualanChart extends ChartWidget
{
    protected static ?string $heading = 'Total Penjualan per Produk';

    protected function getData(): array
    {
        $data = PenjualanBarang::with('produk')
            ->selectRaw('produk_id, SUM(jumlah * harga) as total')
            ->groupBy('produk_id')
            ->get()
            ->mapWithKeys(function ($item) {
                $namaProduk = $item->produk->nama_produk ?? 'Produk #' . $item->produk_id;
                return [$namaProduk => $item->total];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => array_values($data->toArray()),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => array_keys($data->toArray()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
