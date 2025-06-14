<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
// use App\Filament\Resources\PenjualanResource\RelationManagers; // Aktifkan jika Anda memiliki Relation Manager
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// use Illuminate\Database\Eloquent\Builder; // Mungkin tidak terpakai langsung di sini
// use Illuminate\Database\Eloquent\SoftDeletingScope; // Jika menggunakan soft delete

use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker; // Tidak terpakai di sini
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\SelectFilter;
use App\Models\PenjualanBarang; // Tidak terpakai langsung di sini
use Illuminate\Support\Facades\DB; // Tidak terpakai langsung di sini
// use Filament\Forms\Components\Actions\Action as FormAction; // Tidak terpakai di sini
use Illuminate\Support\HtmlString; // <-- PASTIKAN IMPORT INI ADA
// use Illuminate\Support\Facades\Log; // Untuk debugging jika diperlukan
// use Filament\Forms\Components\View; // Tidak terpakai di sini, placeholder digunakan untuk ringkasan

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action as Tableaction;
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Penjualan';
    protected static ?string $navigationGroup = 'Transaksi';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Pesanan
                    Wizard\Step::make('Pesanan')
                        ->icon('heroicon-m-identification')
                        ->schema([
                            Forms\Components\Section::make('Faktur')
                                ->icon('heroicon-m-document-duplicate')
                                ->schema([
                                    TextInput::make('no_faktur')
                                        ->default(fn () => Penjualan::getKodeFaktur())
                                        ->label('Nomor Faktur')
                                        ->required()
                                        ->readonly()
                                        ->columnSpan(1),
    
                                    DateTimePicker::make('tgl')
                                        ->default(now())
                                        ->label('Tanggal Transaksi')
                                        ->required()
                                        ->columnSpan(1),
    
                                    Select::make('pelanggan_id')
                                        ->label('Pelanggan')
                                        ->options(Pelanggan::pluck('nama_pelanggan', 'id_pelanggan')->toArray())
                                        ->required()
                                        ->searchable()
                                        ->placeholder('Pilih Pelanggan')
                                        ->columnSpan(1),
    
                                    TextInput::make('tagihan')
                                        ->default(0)
                                        ->hidden()
                                        ->columnSpan(3),
    
                                    TextInput::make('status')
                                        ->default('pesan')
                                        ->hidden()
                                        ->dehydrated(true),
                                ])
                                ->collapsible()
                                ->columns(3),
                        ]),
    
                    // Step 2: Pilih Barang
                    Wizard\Step::make('Pilih Barang')
                        ->icon('heroicon-m-shopping-bag')
                        ->schema([
                            Repeater::make('items')
                                ->relationship('penjualanBarang')
                                ->label('Detail Barang')
                                ->schema([
                                    Select::make('produk_id')
                                        ->label('Produk')
                                        ->options(Produk::where('stok', '>', 0)->pluck('nama_produk', 'id')->toArray())
                                        ->required()
                                        ->reactive()
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Pilih Produk')
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $produk = Produk::find($state);
                                                $harga = $produk ? $produk->harga : 0;
                                                $set('harga', $harga);
                                            } else {
                                                $set('harga', 0);
                                            }
                                        })
                                        ->columnSpan(['md' => 4]),
    
                                    TextInput::make('harga')
                                        ->label('Harga Satuan')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->readonly()
                                        ->required()
                                        ->dehydrated()
                                        ->columnSpan(['md' => 3]),
    
                                    TextInput::make('jumlah')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->reactive()
                                        ->required()
                                        ->columnSpan(['md' => 2]),
    
                                    Placeholder::make('subtotal_item')
                                        ->label('Subtotal')
                                        ->content(function (Get $get): string {
                                            $harga = $get('harga') ?? 0;
                                            $jumlah = $get('jumlah') ?? 0;
                                            return 'Rp ' . number_format((float)$harga * (int)$jumlah, 0, ',', '.');
                                        })
                                        ->columnSpan(['md' => 3]),
                                ])
                                ->columns(['md' => 12])
                                ->addable()
                                ->deletable()
                                ->reorderable()
                                ->createItemButtonLabel('Tambah Item Produk')
                                ->minItems(1)
                                ->required()
                               ,

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('Simpan Sementara')
                                ->action(function ($get) {
                                    $penjualan = Penjualan::updateOrCreate(
                                        ['no_faktur' => $get('no_faktur')],
                                        [
                                            'tgl' => $get('tgl'),
                                            'pelanggan_id' => $get('pelanggan_id'),
                                            'status' => 'pesan',
                                            'tagihan' => 0,
                                        ]
                                    );
                                    //simpan data barabf
                                    foreach ($get('items') as $item) {
                                        PenjualanBarang::updateOrCreate(
                                            [
                                                'penjualan_id' => $penjualan->id,
                                                'produk_id' => $item['produk_id'],
                                            ],
                                            [
                                                'harga' => $item['harga'],
                                                'jumlah' => $item['jumlah'],
                                            ]
                                        );
                                        // kurangi stok produk
                                        $produk = Produk::find($item['produk_id']);
                                        if ($produk) {
                                            $produk->decrement('stok', $item['jumlah']);
                                        }
                                    }
                                    //hitung total tagihan
                                    $totalTagihan = PenjualanBarang::where('penjualan_id', $penjualan->id)
                                        ->sum(DB::raw('harga * jumlah'));
                                    //update tagihan di penjualan
                                    $penjualan->update(['tagihan' => $totalTagihan]);
                                                                  })
                                ->label('Proses')
                                ->color('primary'),
                        ])
                //
            ])
            ,
    
                    // Step 3: Pembayaran
                    Wizard\Step::make('Pembayaran')
                        ->schema([
                            Placeholder::make('Tabel Pembayaran')
                                ->content(fn (Get $get) => view('filament.penjualan-table', [
                                    'pembayarans' => Penjualan::where('no_faktur', $get('no_faktur'))->get()
                                ])),
                        ]),
                ])
                ->columnSpan(3),
            ])
            ->columns(3);
    }
    

 

            public static function table(Table $table): Table
            {
                return $table
                    ->columns([
                        TextColumn::make('no_faktur')->label('No Faktur')->searchable(),
                        TextColumn::make('pelanggan.nama_pelanggan') // Relasi ke nama pembeli
                            ->label('Nama Pembeli')
                            ->sortable()
                            ->searchable(),
                        TextColumn::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'bayar' => 'success',
                                'pesan' => 'warning',
                            }),
                        TextColumn::make('tagihan')
                            ->formatStateUsing(fn (string|int|null $state): string => rupiah($state))
                            // ->extraAttributes(['class' => 'text-right']) // Tambahkan kelas CSS untuk rata kanan
                            ->sortable()
                            ->alignment('end') // Rata kanan
                        ,
                        TextColumn::make('created_at')->label('Tanggal')->dateTime(),
                    ])
                    ->filters([
                        SelectFilter::make('status')
                            ->label('Filter Status')
                            ->options([
                                'pesan' => 'Pemesanan',
                                'bayar' => 'Pembayaran',
                            ])
                            ->searchable()
                            ->preload(), // Menampilkan semua opsi saat filter diklik
                    ])
                    ->actions([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                    ])
                    //tombol tambahan
                    ->headerActions([
                       Tableaction::make('downloadPdf')
                            ->label('Unduh PDF')
                            ->icon('heroicon-o-document-arrow-down')
                            ->color('success')
                            ->action(function () {
                                $penjualan = Penjualan::all();
                    
                                $pdf = Pdf::loadView('pdf.penjualan', ['penjualan' => $penjualan]);
                    
                                return response()->streamDownload(
                                    fn () => print($pdf->output()),
                                    'pelanggan-list.pdf'
                                );
                            }),
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
                        'index' => Pages\ListPenjualans::route('/'),
                        'create' => Pages\CreatePenjualan::route('/create'),
                        'edit' => Pages\EditPenjualan::route('/{record}/edit'),
                    ];
                }
            }