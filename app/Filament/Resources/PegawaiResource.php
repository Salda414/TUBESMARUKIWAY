<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_pegawai')
                    ->label('ID Pegawai')
                    ->disabled()
                    ->dehydrated()
                    ->default(fn () => str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT)),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan'
                    ])
                    ->required(),

                Forms\Components\TextInput::make('alamat')
                    ->label('Alamat')
                    ->required()
                    ->maxLength(500),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(Pegawai::class, 'email', ignoreRecord: true), // Perbaikan agar tidak error saat edit

                Forms\Components\TextInput::make('no_telpon')
                    ->label('No Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(15),

                    Forms\Components\Select::make('posisi')
                    ->label('Posisi')
                    ->options([
                        'kasir' => 'Kasir',
                        'helper' => 'Helper',
                        'cheff' => 'Cheff',
                        'admin' => 'Admin',
                    ])
                    ->required(),
                

                Forms\Components\TextInput::make('gaji')
                    ->label('Gaji')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_pegawai')->label('ID Pegawai')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('nama')->label('Nama')->sortable()->searchable(),
                Tables\Columns\BadgeColumn::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->sortable()
                ->colors([
                    'primary' => 'Laki-laki',
                    'success' => 'Perempuan',
                ]),
            
                Tables\Columns\TextColumn::make('alamat')->label('Alamat')->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('no_telpon')->label('No Telepon')->sortable(),
                Tables\Columns\TextColumn::make('posisi')->label('Posisi')->sortable(),
                Tables\Columns\TextColumn::make('gaji')
                    ->label('Gaji')
                    ->sortable()
                    ->money('IDR'), // Menambahkan format uang
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan'
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Tambahkan aksi View
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama', 'asc'); // Default urut berdasarkan nama
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan relasi jika ada
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
