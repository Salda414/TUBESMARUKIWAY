<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// untuk form dan table
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;

// untuk model ke user
use App\Models\User;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    // merubah nama label menjadi Vendor
    protected static ?string $navigationLabel = 'Vendor';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_vendor')
                    ->default(fn () => Vendor::getKodeVendor()) // Ambil default dari method getKodeVendor
                    ->label('Kode Vendor')
                    ->required()
                    ->readonly() // Membuat field menjadi read-only
                ,
                TextInput::make('nama_vendor')
                    ->required()
                    ->placeholder('Masukkan nama vendor') // Placeholder untuk membantu pengguna
                    // ->live()
                ,
                TextInput::make('alamat')
                    ->required()
                    ->placeholder('Masukkan alamat vendor') // Placeholder untuk membantu pengguna
                ,
                TextInput::make('telepon')
                    ->required()
                    ->placeholder('Masukkan nomor telepon') // Placeholder untuk membantu pengguna
                    ->numeric() // Validasi agar hanya angka yang diizinkan
                    ->prefix('+62') // Contoh: Menambahkan prefix jika diperlukan
                    ->extraAttributes(['pattern' => '^[0-9]+$', 'title' => 'Masukkan angka yang diawali dengan 0']) // Validasi dengan pattern regex
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_vendor'),
                TextColumn::make('nama_vendor'),
                TextColumn::make('alamat'),
                TextColumn::make('telepon'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}