<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AbsensiStatCards extends BaseWidget
{
    protected function getStats(): array
    {
        $hadirCount = Absensi::where('status', 'hadir')->count();
        $izinCount = Absensi::where('status', 'izin')->count();
        $sakitCount = Absensi::where('status', 'sakit')->count();
        $alpaCount = Absensi::where('status', 'alpa')->count();

        return [
            Stat::make('Jumlah Hadir', $hadirCount)
                ->description('Total pegawai yang hadir')
                ->color('success'),

            Stat::make('Jumlah Izin', $izinCount)
                ->description('Total pegawai yang izin')
                ->color('warning'),

            Stat::make('Jumlah Sakit', $sakitCount)
                ->description('Total pegawai yang sakit')
                ->color('info'),

            Stat::make('Jumlah Alpa', $alpaCount)
                ->description('Total pegawai tidak hadir tanpa keterangan')
                ->color('danger'),
        ];
    }

    
}
