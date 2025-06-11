<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BahanResource\Pages;
use App\Models\Bahan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class BahanResource extends Resource
{
    protected static ?string $model = Bahan::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Bahan';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_bahan')
                    ->label('Kode Bahan')
                    ->disabled() // read-only
                    ->default(fn () => 'KB-' . str_pad((int) (Bahan::max('id') + 1), 5, '0', STR_PAD_LEFT)),

                TextInput::make('nama_bahan')
                    ->label('Nama Bahan')
                    ->required()
                    ->maxLength(100),

                TextInput::make('satuan')
                    ->label('Satuan')
                    ->required()
                    ->placeholder('contoh: kg, liter, pcs'),

                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->default(0),

                TextInput::make('harga_bahan')
                    ->label('Harga Bahan')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_bahan')->label('Kode')->searchable(),
                TextColumn::make('nama_bahan')->label('Nama')->searchable(),
                TextColumn::make('satuan')->label('Satuan'),
                TextColumn::make('stok')->label('Stok')->sortable(),
                TextColumn::make('harga_bahan')
                    ->label('Harga')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBahans::route('/'),
            'create' => Pages\CreateBahan::route('/create'),
            'edit' => Pages\EditBahan::route('/{record}/edit'),
        ];
    }
}
