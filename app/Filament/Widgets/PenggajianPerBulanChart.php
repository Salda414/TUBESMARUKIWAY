<?php 
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penggajian;
// use App\Models\Penggajians;

use Carbon\Carbon;

class PenggajianPerBulanChart extends ChartWidget
{
    // protected static ?string $heading = 'Penggajian Per Bulan '+date('Y'); // Judul widget chart
    protected static ?string $heading = null; // biarkan null

    public function getHeading(): string
    {
        return 'Penggajian Per Bulan ' . date('Y');
    }

    

    // Mendapatkan data untuk chart
    protected function getData(): array
    {
        // Tahun yang ingin ditampilkan
        $year = now()->year;

        // Ambil data total penggajian berdasarkan rumus (harga_jual - harga_beli) * jumlah
        $penggajian = Penggajian::query()
            ->where('status_pembayaran', 'dibayar')
            ->whereYear('periode_awal', $year)
            ->selectRaw('MONTH(periode_awal) as month, SUM(total_gaji) as total_gaji')
            ->groupBy('month')
            ->pluck('total_gaji', 'month');
            // dd($data); // untuk melihat data sebelum dikirim ke chart

         // Siapkan semua bulan (1–12)
         $allMonths = collect(range(1, 12));

         // Gabungkan semua bulan dengan hasil orders
        $data = $allMonths->map(function ($month) use ($penggajian) {
            return $penggajian->get($month, 0);
        });

        $labels = $allMonths->map(function ($month) {
            return Carbon::create()->month($month)->locale('id')->translatedFormat('F'); // Januari, Februari, ...
        });

        // Mengembalikan data dalam format yang dibutuhkan untuk chart
        return [
            'datasets' => [
                [
                    'label' => 'Total Gaji',
                    'data' => $data, // Data untuk chart
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $labels, // Label untuk sumbu X
        ];
    }

    // Jenis chart yang digunakan, misalnya bar chart
    protected function getType(): string
    {
        return 'line'; // Tipe chart bisa diganti sesuai kebutuhan, seperti 'line', 'pie', dll.
    }
}
