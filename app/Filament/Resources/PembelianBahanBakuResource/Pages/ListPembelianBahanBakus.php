<?php

namespace App\Filament\Resources\PembelianBahanBakuResource\Pages;

use App\Filament\Resources\PembelianBahanBakuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembelianBahanBakus extends ListRecords
{
    protected static string $resource = PembelianBahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

