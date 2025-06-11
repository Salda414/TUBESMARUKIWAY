<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penjualan;

class PenjualanPerPelangganChart extends ChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return 'Distribusi Penjualan per Pelanggan ' . date('Y');
    }

    protected function getData(): array
    {
        $year = now()->year;

        $penjualanPerPelanggan = Penjualan::query()
            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id_pelanggan')
            ->where('penjualan.status', 'bayar')
            ->whereYear('penjualan.tgl', $year)
            ->selectRaw('pelanggan.nama_pelanggan, SUM(penjualan.tagihan) as total_penjualan')
            ->groupBy('pelanggan.nama_pelanggan')
            ->orderByDesc('total_penjualan')
            ->pluck('total_penjualan', 'pelanggan.nama_pelanggan');

        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#E7E9ED', '#8B0000', '#008080', '#FFD700',
            '#A52A2A', '#7FFFD4', '#DAA520', '#800080', '#00CED1'
        ];

        $backgroundColors = collect($colors)
            ->take($penjualanPerPelanggan->count())
            ->pad($penjualanPerPelanggan->count(), $colors)
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $penjualanPerPelanggan->values(),
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $penjualanPerPelanggan->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // ✅ PIE CHART!
    }
}
