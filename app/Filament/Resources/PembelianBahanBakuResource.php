<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianBahanBakuResource\Pages;
use App\Models\PembelianBahanBaku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PembelianBahanBakuResource extends Resource
{
    protected static ?string $model = PembelianBahanBaku::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Pembelian Bahan Baku';
    protected static ?string $modelLabel = 'Pembelian';
    protected static ?string $pluralModelLabel = 'Data Pembelian Bahan Baku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_produk')
                    ->label('Produk')
                    ->required(),

                Forms\Components\Select::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'nama_vendor')
                    ->required(),

                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Forms\Components\TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_pembelian')
                    ->label('Tanggal Pembelian')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_produk')
                    ->label('Produk')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('vendor.nama_vendor')
                    ->label('Vendor')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignRight(),

                Tables\Columns\TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR', true)
                    ->alignRight(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(fn ($record) => $record->jumlah * $record->harga_satuan)
                    ->money('IDR', true)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('tanggal_pembelian')
                    ->label('Tanggal Pembelian')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianBahanBakus::route('/'),
            'create' => Pages\CreatePembelianBahanBaku::route('/create'),
            'edit' => Pages\EditPembelianBahanBaku::route('/{record}/edit'),
        ];
    }
}
