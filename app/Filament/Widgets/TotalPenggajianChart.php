<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penggajian;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class TotalPenggajianChart extends ChartWidget
{
    protected static ?string $heading = 'Total Gaji Pegawai Tahun 2025'; 

    protected function getData(): array
    {
        // Ambil data total gaji per pegawai
        $data = Penggajian::query()
            ->select('pegawai_id', DB::raw('SUM(total_gaji) as total_gaji'))
            ->where('status_pembayaran', 'dibayar') // hanya yang sudah dibayar
            ->groupBy('pegawai_id')
            ->with('pegawai') // eager load relasi pegawai
            ->get();

        $labels = Penggajian::selectRaw("DATE_FORMAT(periode_awal, '%Y-%m') as bulan")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('bulan');

        $pegawais = Pegawai::all();
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        $datasets = [];
        $i = 0;

        foreach ($pegawais as $pegawai) {
        $data = [];

        foreach ($labels as $bulan) {
        $totalGaji = Penggajian::where('pegawai_id', $pegawai->id_pegawai)
            ->whereRaw("DATE_FORMAT(periode_awal, '%Y-%m') = ?", [$bulan])
            ->sum('total_gaji');
        $data[] = $totalGaji;
        }

        $datasets[] = [
        'label' => $pegawai->nama,
        'data' => $data,
        'backgroundColor' => $colors[$i % count($colors)],
                    ];
        $i++;
        }

return [
    'datasets' => $datasets,
    'labels' => $labels,
];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
