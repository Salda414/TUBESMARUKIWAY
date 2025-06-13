<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianBahanBakuResource\Pages;
use App\Filament\Resources\PembelianBahanBakuResource\RelationManagers;
use App\Models\PembelianBahanBaku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembelianBahanBakuResource extends Resource
{
    protected static ?string $model = PembelianBahanBaku::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Pembelian Bahan Baku';
    protected static ?string $modelLabel = 'Pembelian Bahan Baku';
    protected static ?string $pluralModelLabel = 'Pembelian Bahan Baku';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Informasi Vendor')
                        ->schema([
                            Forms\Components\Select::make('vendor_id')
                                ->label('Vendor')
                                ->required()
                                ->relationship('vendor', 'nama_vendor')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    $vendor = \App\Models\Vendor::find($state);
                                    if ($vendor) {
                                        $set('vendor_info', "Alamat: {$vendor->alamat}\nTelepon: {$vendor->telepon}");
                                    }
                                }),

                            Forms\Components\Textarea::make('vendor_info')
                                ->label('Info Vendor')
                                ->disabled()
                                ->columnSpanFull(),

                            Forms\Components\DatePicker::make('tanggal_pembelian')
                                ->label('Tanggal Pembelian')
                                ->required()
                                ->default(now()),
                        ]),

                    Forms\Components\Wizard\Step::make('Items Pembelian')
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    Forms\Components\TextInput::make('nama_bahan')
                                        ->label('Nama Bahan')
                                        ->required(),

                                    Forms\Components\TextInput::make('harga')
                                        ->label('Harga Satuan')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                            $set('subtotal', $state * $get('jumlah'));
                                        }),

                                    Forms\Components\TextInput::make('jumlah')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                            $set('subtotal', $state * $get('harga'));
                                        }),

                                    Forms\Components\Select::make('satuan')
                                        ->label('Satuan')
                                        ->options([
                                            'kg' => 'Kilogram',
                                            'g' => 'Gram',
                                            'l' => 'Liter',
                                            'ml' => 'Mililiter',
                                            'pcs' => 'Pieces',
                                        ])
                                        ->required(),

                                    Forms\Components\TextInput::make('subtotal')
                                        ->label('Subtotal')
                                        ->numeric()
                                        ->disabled(),
                                ])
                                ->columns(5)
                                ->defaultItems(1)
                                ->live()
                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                    $total = collect($get('items'))->sum('subtotal');
                                    $set('total_harga', $total);
                                })
                                ->deleteAction(
                                    fn (Forms\Components\Actions\Action $action) => $action->after(fn (Forms\Get $get, Forms\Set $set) => [
                                        $set('total_harga', collect($get('items'))->sum('subtotal')),
                                    ]),
                                ),
                        ]),

                    Forms\Components\Wizard\Step::make('Pembayaran')
                        ->schema([
                            Forms\Components\TextInput::make('total_harga')
                                ->label('Total Harga')
                                ->numeric()
                                ->readOnly(),

                            Forms\Components\Select::make('status')
                                ->label('Status Pembayaran')
                                ->options([
                                    'dibayar' => 'Dibayar',
                                ])
                                ->required()
                                ->default('bayar'),

                            Forms\Components\Textarea::make('catatan')
                                ->label('Catatan')
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.nama_vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_pembelian')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items')
    ->label('Nama Bahan Baku')
    ->formatStateUsing(fn ($record) =>
        $record->items->pluck('nama_bahan')->implode(', ')
    )
    ->limit(50)
    ->tooltip(fn ($record) =>
        $record->items->pluck('nama_bahan')->implode(', ')
    ),


                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
    ->label('Status')
    ->badge()
    ->colors([
        'primary' => 'draft',
        'warning' => 'diproses',
        'info' => 'dikirim',
        'success' => 'dibayar',
        'gray' => 'selesai',
    ])
    ->formatStateUsing(fn ($state) => ucfirst($state))
    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'diproses' => 'Diproses',
                        'dibayar' => 'Dibayar',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('bayar')
                    ->label('Bayar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (PembelianBahanBaku $record) => $record->status !== 'dibayar' && $record->status !== 'selesai')
                    ->action(function (PembelianBahanBaku $record) {
                        $record->update(['status' => 'dibayar']);
                    }),

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
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianBahanBaku::route('/'),
            'create' => Pages\CreatePembelianBahanBaku::route('/create'),
            'edit' => Pages\EditPembelianBahanBaku::route('/{record}/edit'),
        ];
    }
}