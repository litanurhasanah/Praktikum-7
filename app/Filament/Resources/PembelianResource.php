<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Models\Pembelian;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    // Ikon di menu samping
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    // Nama Group di menu samping
    protected static ?string $navigationGroup = 'Transaksi';

    // Nama Label di menu samping
    protected static ?string $navigationLabel = 'Pembelian Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: HEADER (Data Supplier & Tanggal)
                Section::make('Data Pembelian')
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'nama') // Menggunakan 'nama' sesuai database Anda
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        DatePicker::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),
                    ])->columns(2),

                // SECTION 2: DETAIL (Repeater Barang)
                Section::make('Daftar Barang')
                    ->schema([
                        Repeater::make('items') // Nama relasi di model Pembelian.php
                            ->relationship()
                            ->schema([
                                Select::make('barang_id')
                                    ->label('Pilih Barang')
                                    ->options(Barang::all()->pluck('nama_barang', 'id'))
                                    ->searchable()
                                    ->reactive()
                                    // Ambil harga otomatis saat barang dipilih
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $barang = Barang::find($state);
                                        if ($barang) {
                                            $set('harga_beli', $barang->harga_barang);
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(4),

                                TextInput::make('jumlah')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('harga_beli') // Menggunakan 'harga_beli' agar tidak error SQL 1364
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(3),
                                
                                // Kalkulasi Subtotal otomatis
                                Placeholder::make('subtotal')
                                    ->label('Subtotal')
                                    ->content(function (Get $get) {
                                        $qty = $get('jumlah') ?? 0;
                                        $harga = $get('harga_beli') ?? 0;
                                        return 'Rp ' . number_format($qty * $harga, 0, ',', '.');
                                    })
                                    ->columnSpan(3),
                            ])
                            ->columns(12) // Mengatur tampilan horizontal
                            ->addActionLabel('Tambah Item Barang')
                            ->reorderable(false)
                            ->defaultItems(1),
                    ]),
                
                // SECTION 3: TOTAL AKHIR
                Section::make('Total Pembayaran')
                    ->schema([
                        Placeholder::make('grand_total')
                            ->label('Total Keseluruhan')
                            ->content(function (Get $get) {
                                $items = $get('items') ?? [];
                                $total = 0;
                                foreach ($items as $item) {
                                    $total += ($item['jumlah'] ?? 0) * ($item['harga_beli'] ?? 0);
                                }
                                return 'Rp ' . number_format($total, 0, ',', '.');
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.nama')
                    ->label('Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jenis Barang')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }
}