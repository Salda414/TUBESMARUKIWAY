<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
// use App\Filament\Resources\PenjualanResource\RelationManagers; // Aktifkan jika Anda memiliki Relation Manager
use App\Models\Penjualan;
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
use Filament\Forms\Components\Hidden; //menggunakan hidden field
use Filament\Tables\Filters\SelectFilter;

// model
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Pembayaran;
use App\Models\PenjualanProduk;

// use App\Models\PenjualanBarang; // Tidak terpakai langsung di sini
use Illuminate\Support\Facades\DB; // Tidak terpakai langsung di sini
use Filament\Forms\Components\Actions\Action as FormAction; // Tidak terpakai di sini
use Illuminate\Support\HtmlString; // <-- PASTIKAN IMPORT INI ADA
// use Illuminate\Support\Facades\Log; // Untuk debugging jika diperlukan

// use Filament\Forms\Components\View; // Tidak terpakai di sini, placeholder digunakan untuk ringkasan

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
                                        ->options(Pelanggan::pluck('nama_pelanggan', 'id')->toArray())
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Pilih Pelanggan')
                                        ->columnSpan(1),
                                    TextInput::make('tagihan')
                                        ->default(0)
                                        ->numeric()
                                        ->readOnly()
                                        ->label('Total Tagihan (Otomatis)') // Label diubah sedikit
                                        ->prefix('Rp')
                                        ->live(debounce: 500)
                                        ->columnSpan(3),
                                    TextInput::make('status')
                                        ->default('pesan')
                                        ->hidden()
                                        ->dehydrated(true),
                                ])
                                ->collapsible()
                                ->columns(3),
                        ]),
                    Wizard\Step::make('Pilih Barang')
                        ->icon('heroicon-m-shopping-bag')
                        ->schema([
                            Repeater::make('items')
                                ->relationship('PenjualanProduk')
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
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                        ->afterStateUpdated(function ($state, Set $set) {
                                            if ($state) {
                                                $produk = Produk::find($state);
                                                $harga = $produk ? $produk->harga : 0;
                                                $set('harga', $harga);
                                            } else {
                                                $set('harga', 0);
                                            }
                                            // Pemanggilan updateTotalTagihan dihapus dari sini
                                        })
                                        ->columnSpan(['md' => 4]),
                                    TextInput::make('harga')
                                        ->label('Harga Satuan')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->readOnly()
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
                                        // Pemanggilan updateTotalTagihan dihapus dari afterStateUpdated di sini
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
                                ->reactive()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updateTotalTagihan($get, $set);
                                }),
                        ]),
                    Wizard\Step::make('Pembayaran')
                        ->icon('heroicon-m-credit-card')
                        ->schema([
                            Placeholder::make('ringkasan_pesanan_placeholder')
                                ->label('Ringkasan Pesanan')
                                ->content(function (Get $get): HtmlString {
                                    $nomorFaktur = $get('no_faktur') ?? 'Belum ada';
                                    $tanggalTransaksi = $get('tgl');
                                    $pelangganId = $get('pelanggan_id');
                                    $items = $get('items') ?? [];
                                    
                                    // Kalkulasi ulang total tagihan berdasarkan items saat ini
                                    $calculatedTotalTagihan = 0;
                                    if (is_array($items)) {
                                        $calculatedTotalTagihan = collect($items)
                                            ->sum(function ($item) {
                                                $harga = is_numeric($item['harga'] ?? null) ? (float)$item['harga'] : 0;
                                                $jumlah = is_numeric($item['jumlah'] ?? null) ? (int)$item['jumlah'] : 0;
                                                return $harga * $jumlah;
                                            });
                                    }
                                    // Anda bisa memilih untuk menggunakan $get('tagihan') jika ingin nilai yang sudah di-set sebelumnya,
                                    // atau $calculatedTotalTagihan untuk nilai yang dihitung saat ini.
                                    // Untuk konsistensi tampilan ringkasan, $calculatedTotalTagihan lebih aman.
                                    $totalTagihanUntukTampilan = $calculatedTotalTagihan;


                                    $namaPelanggan = 'Pelanggan tidak dipilih';
                                    if ($pelangganId) {
                                        $pelanggan = \App\Models\Pelanggan::find($pelangganId);
                                        if ($pelanggan) {
                                            $namaPelanggan = $pelanggan->nama_pelanggan;
                                        }
                                    }
                                    $html = "<div class='p-2 border rounded-md bg-gray-50 dark:bg-gray-800 dark:border-gray-700 space-y-4'>";
                                    $html .= "<h3 class='text-lg font-semibold text-gray-900 dark:text-white'>Detail Faktur</h3>";
                                    $html .= "<table class='w-full text-sm text-left text-gray-700 dark:text-gray-300'>";
                                    $html .= "<tbody>";
                                    $html .= "<tr class='border-b dark:border-gray-700'><td class='py-2 font-medium text-gray-800 dark:text-gray-200 pr-2'>No. Faktur:</td><td class='py-2'>" . htmlspecialchars($nomorFaktur) . "</td></tr>";
                                    if ($tanggalTransaksi) {
                                        try {
                                            $formattedDate = \Carbon\Carbon::parse($tanggalTransaksi)->translatedFormat('d F Y H:i');
                                            $html .= "<tr class='border-b dark:border-gray-700'><td class='py-2 font-medium text-gray-800 dark:text-gray-200 pr-2'>Tanggal:</td><td class='py-2'>" . htmlspecialchars($formattedDate) . "</td></tr>";
                                        } catch (\Exception $e) {
                                            $html .= "<tr class='border-b dark:border-gray-700'><td class='py-2 font-medium text-gray-800 dark:text-gray-200 pr-2'>Tanggal:</td><td class='py-2'>Tanggal tidak valid</td></tr>";
                                        }
                                    } else {
                                        $html .= "<tr class='border-b dark:border-gray-700'><td class='py-2 font-medium text-gray-800 dark:text-gray-200 pr-2'>Tanggal:</td><td class='py-2'>Belum diisi</td></tr>";
                                    }
                                    $html .= "<tr><td class='py-2 font-medium text-gray-800 dark:text-gray-200 pr-2'>Pelanggan:</td><td class='py-2'>" . htmlspecialchars($namaPelanggan) . "</td></tr>";
                                    $html .= "</tbody></table>";
                                    $html .= "<h4 class='text-md font-semibold mt-4 text-gray-900 dark:text-white'>Item Dibeli:</h4>";
                                    if (empty($items)) {
                                        $html .= "<p class='text-gray-600 dark:text-gray-400'>Belum ada item yang dipilih.</p>";
                                    } else {
                                        $html .= "<table class='w-full text-sm text-left text-gray-700 dark:text-gray-300 table-auto border-collapse border border-gray-300 dark:border-gray-600'>";
                                        $html .= "<thead class='text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400'><tr>";
                                        $html .= "<th scope='col' class='px-4 py-2 border border-gray-300 dark:border-gray-600'>Nama Produk</th>";
                                        $html .= "<th scope='col' class='px-4 py-2 border border-gray-300 dark:border-gray-600 text-center'>Jumlah</th>";
                                        $html .= "<th scope='col' class='px-4 py-2 border border-gray-300 dark:border-gray-600 text-right'>Harga Satuan</th>";
                                        $html .= "<th scope='col' class='px-4 py-2 border border-gray-300 dark:border-gray-600 text-right'>Subtotal</th>";
                                        $html .= "</tr></thead><tbody>";
                                        foreach ($items as $item) {
                                            $produkId = $item['produk_id'] ?? null;
                                            $namaProduk = 'Produk tidak dikenal';
                                            if ($produkId) {
                                                $produk = \App\Models\Produk::find($produkId);
                                                if ($produk) {
                                                    $namaProduk = $produk->nama_produk;
                                                }
                                            }
                                            $jumlah = $item['jumlah'] ?? 0;
                                            $harga = $item['harga'] ?? 0;
                                            $subtotal = (float)$jumlah * (float)$harga;
                                            $html .= "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'>";
                                            $html .= "<td class='px-4 py-2 border border-gray-300 dark:border-gray-600'>" . htmlspecialchars($namaProduk) . "</td>";
                                            $html .= "<td class='px-4 py-2 border border-gray-300 dark:border-gray-600 text-center'>" . htmlspecialchars($jumlah) . "</td>";
                                            $html .= "<td class='px-4 py-2 border border-gray-300 dark:border-gray-600 text-right'>Rp " . number_format($harga, 0, ',', '.') . "</td>";
                                            $html .= "<td class='px-4 py-2 border border-gray-300 dark:border-gray-600 text-right'>Rp " . number_format($subtotal, 0, ',', '.') . "</td></tr>";
                                        }
                                        $html .= "</tbody></table>";
                                    }
                                    $html .= "<div class='mt-4 text-right'><h4 class='text-lg font-semibold text-gray-900 dark:text-white'>Total Tagihan: Rp " . number_format($totalTagihanUntukTampilan, 0, ',', '.') . "</h4></div>";
                                    $html .= "</div>";
                                    return new HtmlString($html);
                                })
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull()
                // ->submitAction(...) // Dihapus untuk menggunakan tombol submit default Wizard
            ]);
    }

    public static function updateTotalTagihan(Get $get, Set $set): void
    {
        // \Log::info('updateTotalTagihan called.');
        $items = $get('items');
        // \Log::info('Items in updateTotalTagihan:', $items ?? []);

        $totalTagihan = 0;
        if (is_array($items)) {
            $totalTagihan = collect($items)
                ->sum(function ($item) {
                    $harga = is_numeric($item['harga'] ?? null) ? (float)$item['harga'] : 0;
                    $jumlah = is_numeric($item['jumlah'] ?? null) ? (int)$item['jumlah'] : 0;
                    // \Log::info('Processing item for sum:', ['harga' => $harga, 'jumlah' => $jumlah, 'subtotal' => $harga * $jumlah]);
                    return $harga * $jumlah;
                });
        }
        // \Log::info('Calculated Total Tagihan:', ['total' => $totalTagihan]);
      
        $set('tagihan', $totalTagihan);
    }

    // ... (sisa kode table, getRelations, getPages tetap sama) ...
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->label('No Faktur')->searchable()->sortable(),
                TextColumn::make('pelanggan.nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->sortable()
                    ->searchable()
                    ->default('-'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bayar', 'lunas' => 'success',
                        'pesan' => 'warning',
                        'kirim' => 'info',
                        'batal' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('tagihan')
                    ->numeric(decimalPlaces: 0, decimalSeparator: ',', thousandsSeparator: '.')
                    ->prefix('Rp ')
                    ->alignEnd()
                    ->label('Total Tagihan')
                    ->sortable(),
                TextColumn::make('tgl')->label('Tanggal Transaksi')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pesan' => 'Pemesanan',
                        'bayar' => 'Sudah Bayar',
                        'lunas' => 'Lunas',
                        'kirim' => 'Dikirim',
                        'batal' => 'Dibatalkan',
                    ])
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tgl', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\PenjualanBarangRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualan::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}