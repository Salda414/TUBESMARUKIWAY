<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Absensi;

class AbsensiChart extends BarChartWidget
{
    protected static ?string $heading = 'Grafik Absensi Pegawai';
    protected static ?int $sort = 1; // urutan tampil

    protected function getData(): array
    {
        $hadir = Absensi::where('status', 'hadir')->count();
        $izin = Absensi::where('status', 'izin')->count();
        $sakit = Absensi::where('status', 'sakit')->count();
        $alpa = Absensi::where('status', 'alpa')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah',
                    'data' => [$hadir, $izin, $sakit, $alpa],
                    'backgroundColor' => [
                        '#4ade80', // Hijau - Hadir
                        '#facc15', // Kuning - Izin
                        '#60a5fa', // Biru - Sakit
                        '#f87171', // Merah - Alpa
                    ],
                ],
            ],
            'labels' => ['Hadir', 'Izin', 'Sakit', 'Alpa'],
        ];
    }
}
