<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Penjualan;
use App\Models\Pelanggan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class DashboardStatCards extends BaseWidget
{
    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : null;

        $endDate = !is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now();

        $isBusinessCustomersOnly = $this->filters['businessCustomersOnly'] ?? null;
        $businessCustomerMultiplier = match (true) {
            boolval($isBusinessCustomersOnly) => 2 / 3,
            blank($isBusinessCustomersOnly) => 1,
            default => 1 / 3,
        };

        $diffInDays = $startDate ? $startDate->diffInDays($endDate) : 0;

        $revenue = (int)(($startDate ? ($diffInDays * 137) : 192100) * $businessCustomerMultiplier);

        $formatNumber = fn (int $number): string =>
            $number < 1000 ? (string) Number::format($number, 0)
            : ($number < 1000000
                ? Number::format($number / 1000, 2) . 'k'
                : Number::format($number / 1000000, 2) . 'm');

        return [
            Stat::make('Total Pelanggan', Pelanggan::count())
                ->description('Jumlah pelanggan terdaftar'),

            Stat::make('Total Transaksi', Penjualan::count())
                ->description('Jumlah transaksi'),

            Stat::make('Total Penjualan', rupiah(
                Penjualan::where('status', 'bayar')->sum('tagihan')
            ))
                ->description('Jumlah transaksi terbayar'),

            Stat::make('Revenue', '$' . $formatNumber($revenue))
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
