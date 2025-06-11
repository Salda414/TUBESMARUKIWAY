<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Penggajian;

class PenggajianChart extends BarChartWidget
{
    protected static ?string $heading = 'Grafik Gaji Pegawai';

    protected function getData(): array
    {
        $data = Penggajian::selectRaw('pegawai_id, SUM(total_gaji) as total')
            ->groupBy('pegawai_id')
            ->with('pegawai')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Gaji',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $data->pluck('pegawai.nama')->toArray(),
        ];
    }
}
