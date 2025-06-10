<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use App\Models\Penggajian;
use Barryvdh\DomPDF\Facade\Pdf; // hanya ini yang perlu
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;

class ListPenggajians extends ListRecords
{
    protected static string $resource = PenggajianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $penggajian = Penggajian::with('pegawai')->get(); // load relasi jika perlu

                    $pdf = Pdf::loadView('pdf.penggajian', [
                        'penggajians' => $penggajian,
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'penggajian-list.pdf'
                    );
                }),
        ];
    }
}
