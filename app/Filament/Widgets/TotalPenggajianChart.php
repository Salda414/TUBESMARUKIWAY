<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penggajian;
use Illuminate\Support\Facades\DB;

class TotalPenggajianChart extends ChartWidget
{
    protected static ?string $heading = 'Total Gaji Pegawai'; 

    protected function getData(): array
    {
        // Ambil data total gaji per pegawai
        $data = Penggajian::query()
            ->select('pegawai_id', DB::raw('SUM(total_gaji) as total_gaji'))
            ->where('status_pembayaran', 'dibayar') // hanya yang sudah dibayar
            ->groupBy('pegawai_id')
            ->with('pegawai') // eager load relasi pegawai
            ->get();

        // mapping nama pegawai
        $labels = $data->map(function ($item) {
            return optional($item->pegawai)->nama ?? 'Tidak diketahui';
        });

        $values = $data->pluck('total_gaji');

        return [
            'datasets' => [
                [
                    'label' => 'Total Gaji',
                    'data' => $values->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
