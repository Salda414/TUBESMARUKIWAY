<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use App\Models\Absensi;
use App\Models\Pegawai;

class AbsensiChart extends BarChartWidget
{
    protected static ?string $heading = 'Grafik Absensi Pegawai';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $labels = [];
        $dataHadir = [];
        $dataTidakHadir = [];

        $pegawaiList = Pegawai::all();

        foreach ($pegawaiList as $pegawai) {
            $labels[] = $pegawai->nama;

            $hadir = Absensi::where('pegawai_id', $pegawai->id_pegawai)
                ->where('status', 'hadir')
                ->count();

            $tidakHadir = Absensi::where('pegawai_id', $pegawai->id_pegawai)
                ->whereIn('status', ['izin', 'sakit', 'alpa'])
                ->count();

            $dataHadir[] = $hadir;
            $dataTidakHadir[] = $tidakHadir;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $dataHadir,
                    'backgroundColor' => '#4ade80', // Hijau
                ],
                [
                    'label' => 'Tidak Hadir',
                    'data' => $dataTidakHadir,
                    'backgroundColor' => '#f87171', // Merah
                ],
            ],
            'labels' => $labels,
        ];
    }
}
