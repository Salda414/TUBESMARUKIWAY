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

// untuk model ke user
use App\Models\User;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                //direlasikan ke tabel user
                Select::make('user_id')
                    ->label('User Id')
                    ->relationship('user', 'email')
                    ->searchable() // Menambahkan fitur pencarian
                    ->preload() // Memuat opsi lebih awal untuk pengalaman yang lebih cepat
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = User::find($state);
                            $set('nama_pelanggan', $user->name);
                        }
                    })
                , 
                TextInput::make('kode_pelanggan')
                    ->default(fn () => Pelanggan::getKodePembeli()) // Ambil default dari method getKodePembeli
                    ->label('Kode Pelanggan')
                    ->required()
                    ->readonly() // Membuat field menjadi read-only
                ,
                Grid::make(1) // Membuat hanya 1 kolom
                ->schema([
                    TextInput::make('nama_pelanggan')
                        ->required()
                        ->placeholder('Masukkan nama pelanggan')
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
                TextColumn::make('kode_pelanggan'),
                TextColumn::make('nama_pelanggan'),
                TextColumn::make('nomor_telepon'),
                TextColumn::make('email'),
                TextColumn::make('alamat'), 
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
