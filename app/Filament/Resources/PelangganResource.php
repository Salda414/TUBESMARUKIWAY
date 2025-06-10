<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\TextInput; //kita menggunakan textinput
use Filament\Forms\Components\Grid;

use Filament\Tables\Columns\TextColumn;

use App\Filament\Resources\PelangganResource\Pages;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

//
use Filament\Forms\Components\Select;
//use Filament\Forms\Components\TextInput;
//
use App\Models\Produk;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Grid::make(1) // Membuat hanya 1 kolom
                ->schema([
                    TextInput::make('nama_pelanggan')
                        ->required()
                        ->placeholder('Masukkan nama pelanggan')
                    ,
                    Select::make('produk_id')
                        ->label('Produk')
                        ->options(Produk::all()->pluck('nama_produk', 'id'))
                        ->searchable()
                        ->required()
                    ,
                    TextInput::make('nomor_telepon')
                        ->required()
                        ->placeholder('Masukkan nomor telepon')
                    ,
                    TextInput::make('email')
                        ->autocapitalize('words')
                        ->label('Email')
                        ->required()
                        ->placeholder('Masukkan email')
                    ,
                    TextInput::make('alamat')
                        ->required()
                        ->placeholder('Masukkan alamat pelanggan')
                    ,
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nama_pelanggan'),
                TextColumn::make('produk.nama_produk')->label('Produk'),
                TextColumn::make('nomor_telepon'),
                TextColumn::make('email'),
                TextColumn::make('alamat'), 
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('produk_id')
                    ->label('Produk')
                    ->relationship('produk', 'nama_produk'),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}
