<?php

namespace App\Filament\Resources\PembelianBahanBakuResource\Pages;

use App\Filament\Resources\PembelianBahanBakuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembelianBahanBaku extends EditRecord
{
    protected static string $resource = PembelianBahanBakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
{
    $data['total_harga'] = collect($data['items'] ?? [])
        ->sum(fn ($item) => floatval($item['harga']) * floatval($item['jumlah']));
    return $data;
}

}