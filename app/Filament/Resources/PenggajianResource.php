<?php

namespace App\Filament\Resources;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\Penggajian;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use App\Filament\Resources\PenggajianResource\Pages;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Tables\Table;

use Barryvdh\DomPDF\Facade\Pdf;
use Illumnate\Support\Facades\Storage;

class PenggajianResource extends Resource
{
    protected static ?string $model = Penggajian::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'Penggajian';

    public static function getNavigationGroup(): ?string
    {
        return 'Kepegawaian';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Data Absensi')
                        ->schema([
                            TextInput::make('no_penggajian')
                                ->label('No Penggajian')
                                ->default(function () {
                                    // Menambahkan logika untuk otomatis mengisi no_penggajian
                                    $today = now()->format('Ymd');
                                    $countToday = Penggajian::whereDate('created_at', now()->toDateString())->count() + 1;
                                    return 'PGJ-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
                                })
                                ->disabled() // Membuat field ini hanya dapat dilihat, tidak dapat diubah
                                ->required(), // Pastikan field ini diperlukan

                            Select::make('pegawai_id')
                                ->label('Pegawai')
                                ->options(Pegawai::all()->pluck('nama', 'id_pegawai'))
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                    $set('jumlah_hadir', 0); // Reset jumlah hadir jika pegawai berubah

                                    $pegawai = Pegawai::find($state);
                                    if ($pegawai) {
                                        $set('gaji_per_hari', $pegawai->gaji_per_hari);
                                    }

                                    // Menambahkan pengecekan untuk periode_awal dan periode_akhir
                                    $periodeAwal = $get('periode_awal');
                                    $periodeAkhir = $get('periode_akhir');

                                    if ($pegawai && $periodeAwal && $periodeAkhir) {
                                        // Hitung jumlah hadir berdasarkan pegawai yang dipilih dan periode yang sudah ditentukan
                                        $jumlahHadir = Absensi::where('pegawai_id', $state)
                                            ->whereBetween('tanggal', [$periodeAwal, $periodeAkhir])
                                            ->where('status', 'hadir')
                                            ->count();

                                        $set('jumlah_hadir', $jumlahHadir);
                                    }
                                })
                                ->required(),

                            DatePicker::make('periode_awal')
                                ->label('Periode Awal')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                    // Reset jumlah hadir jika periode awal diubah
                                    $set('jumlah_hadir', 0);
                                    $periodeAkhir = $get('periode_akhir');
                                    $pegawaiId = $get('pegawai_id');

                                    // Jika periode akhir sudah dipilih, hitung jumlah hadir
                                    if ($periodeAkhir && $pegawaiId) {
                                        $jumlahHadir = Absensi::where('pegawai_id', $pegawaiId)
                                            ->whereBetween('tanggal', [$state, $periodeAkhir])
                                            ->where('status', 'hadir')
                                            ->count();
                                        $set('jumlah_hadir', $jumlahHadir);
                                    }
                                }),

                            DatePicker::make('periode_akhir')
                                ->label('Periode Akhir')
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                    // Reset jumlah hadir jika periode akhir diubah
                                    $set('jumlah_hadir', 0);
                                    $periodeAwal = $get('periode_awal');
                                    $pegawaiId = $get('pegawai_id');

                                    // Jika periode awal sudah dipilih, hitung jumlah hadir
                                    if ($periodeAwal && $pegawaiId) {
                                        $jumlahHadir = Absensi::where('pegawai_id', $pegawaiId)
                                            ->whereBetween('tanggal', [$periodeAwal, $state])
                                            ->where('status', 'hadir')
                                            ->count();
                                        $set('jumlah_hadir', $jumlahHadir);
                                    }
                                }),

                            TextInput::make('jumlah_hadir')
                                ->label('Jumlah Hadir')
                                ->numeric()
                                ->reactive()
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            TextInput::make('gaji_per_hari')
                                ->label('Gaji per Hari')
                                ->numeric()
                                ->reactive()
                                ->dehydrated()
                                ->required()
                                ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                    $jumlahHadir = $get('jumlah_hadir');
                                    if (is_numeric($jumlahHadir) && is_numeric($state)) {
                                        $set('total_gaji', $jumlahHadir * $state);
                                    }
                                }),
                        ]),

                    Wizard\Step::make('Perhitungan Gaji')
                        ->schema([
                            TextInput::make('total_gaji')
                                ->label('Total Gaji')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            Select::make('status_pembayaran')
                                ->label('Status Pembayaran')
                                ->options([
                                    'belum_dibayar' => 'Belum Dibayar',
                                    'dibayar' => 'Dibayar',
                                ])
                                ->default('dibayar')
                                ->required(),
                        ]),
                ])
                    ->submitAction(
                        Action::make('save')
                            ->label('Bayar')
                            ->submit('save')
                            ->action(function (array $data) {
                                $today = now()->format('Ymd');
                                $countToday = Penggajian::whereDate('created_at', now()->toDateString())->count() + 1;
                                $noPenggajian = 'PGJ-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

                                Penggajian::create([
                                    'no_penggajian' => $noPenggajian,
                                    'pegawai_id' => $data['pegawai_id'],
                                    'jumlah_hadir' => $data['jumlah_hadir'],
                                    'gaji_per_hari' => $data['gaji_per_hari'],
                                    'total_gaji' => $data['total_gaji'],
                                    'status_pembayaran' => $data['status_pembayaran'],
                                    'periode_awal' => $data['periode_awal'],
                                    'periode_akhir' => $data['periode_akhir'],
                                ]);

                                Notification::make()
                                    ->title('Pembayaran berhasil disimpan')
                                    ->success()
                                    ->send();
                            })
                    )

                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_penggajian')
                    ->label('No Penggajian')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('pegawai.nama')
                    ->label('Nama Pegawai')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('jumlah_hadir')
                    ->label('Jumlah Hadir')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('gaji_per_hari')
                    ->label('Gaji per Hari')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_gaji')
                    ->label('Total Gaji')
                    ->money('IDR')
                    ->sortable(),

                IconColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->icon(fn (string $state): string => match ($state) {
                        'dibayar' => 'heroicon-o-check-circle',
                        'belum_dibayar' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'dibayar' => 'success',
                        'belum_dibayar' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->options([
                        'dibayar' => 'Dibayar',
                        'belum_dibayar' => 'Belum Dibayar',
                    ])
                    ->label('Status Pembayaran'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('Kirim Slip Gaji')
                    ->label('Kirim Slip Gaji')
                    ->url(fn (Penggajian $record) => route('penggajian.kirim', $record->id))
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->status_pembayaran == 'dibayar'),
        ])      
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenggajians::route('/'),
            'create' => Pages\CreatePenggajian::route('/create'),
            'edit' => Pages\EditPenggajian::route('/{record}/edit'),
        ];
    }
}
