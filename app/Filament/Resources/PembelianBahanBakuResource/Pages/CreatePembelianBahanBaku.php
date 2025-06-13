<?php

namespace App\Filament\Resources\PembelianBahanBakuResource\Pages;

use App\Filament\Resources\PembelianBahanBakuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;



class CreatePembelianBahanBaku extends CreateRecord
{
    protected static string $resource = PembelianBahanBakuResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['total_harga'] = collect($data['items'] ?? [])
        ->sum(fn ($item) => floatval($item['harga']) * floatval($item['jumlah']));
    return $data;
}

}