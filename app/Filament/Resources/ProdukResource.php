<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $pluralLabel = 'Produk';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('nama_produk')
                    ->label('Nama Produk')
                    ->required(),

                Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(Kategori::all()->pluck('jenis_kategori', 'id')->toArray())
                    ->required(),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->nullable(),
                
                FileUpload::make('gambar')
                    ->label('Gambar')
                    ->image()
                    ->directory('images')
                    ->required(),

                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->minValue(0) // Nilai minimal 0 (opsional jika tidak ingin ada harga negatif)
                ->reactive() // Menjadikan input reaktif terhadap perubahan
                ->extraAttributes(['id' => 'harga']) // Tambahkan ID untuk pengikatan JavaScript
                ->placeholder('Masukkan harga barang') // Placeholder untuk membantu pengguna
                ->live()
                ->afterStateUpdated(fn ($state, callable $set) => 
                    $set('harga_barang', number_format((int) str_replace('.', '', $state), 0, ',', '.'))
                  )
                ,

                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Tidak Tersedia' => 'Tidak Tersedia',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_produk')->label('Nama Produk'),
                TextColumn::make('kategori.jenis_kategori')->label('Kategori'),
                TextColumn::make('harga')
                ->label('Harga')
                ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                    ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                    ->sortable(),
                TextColumn::make('stok')->label('Stok'),
                TextColumn::make('status')->label('Status'),
                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->size(50)            // ukuran thumbnail
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'jenis_kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
