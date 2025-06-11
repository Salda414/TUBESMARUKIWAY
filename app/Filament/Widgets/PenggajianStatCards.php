<?php

namespace App\Filament\Widgets;

use App\Models\Penggajian;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PenggajianStatCards extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Gaji Keseluruhan', 'Rp ' . number_format(Penggajian::sum('total_gaji'), 0, ',', '.')),
            Card::make('Rata-rata Gaji', 'Rp ' . number_format(Penggajian::avg('total_gaji'), 0, ',', '.')),
            Card::make('Jumlah Transaksi Gaji', Penggajian::count()),
        ];
    }
}
