<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table; // Pastikan menggunakan Filament\Tables\Table, bukan Filament\Resources\Table
use App\Filament\Resources\AbsensiResource\Pages\ViewAbsensi;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $navigationGroup = 'Kepegawaian';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('pegawai_id')
                ->label('Nama Pegawai')
                ->options(Pegawai::pluck('nama', 'id_pegawai'))
                ->searchable()
                ->required(),



            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status Kehadiran')
                ->options([
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpa' => 'Alpa',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.id_pegawai')
                ->label('ID Pegawai'),

                Tables\Columns\TextColumn::make('pegawai.nama')->label('Nama Pegawai'),
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable()->label('Tanggal'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'izin',
                        'info'    => 'sakit',
                        'danger'  => 'alpa',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->since()->label('Dibuat'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
           'view' => ViewAbsensi::route('/{record}'),
        ];
    }
}