<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    // Tambahkan Navigation Group agar rapi
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        TextInput::make('kode_barang')
                            ->default(fn () => Barang::getKodeBarang())
                            ->label('Kode Barang')
                            ->required()
                            ->readonly(),

                        TextInput::make('nama_barang')
                            ->required()
                            ->placeholder('Masukkan nama barang'),

                        TextInput::make('harga_barang')
                            ->label('Harga Jual')
                            ->numeric() // Gunakan numeric agar Filament otomatis memvalidasi angka
                            ->prefix('Rp')
                            ->required()
                            ->placeholder('Masukkan harga barang'),

                        TextInput::make('stok')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->placeholder('Masukkan stok awal'),

                        TextInput::make('rating')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(5)
                            ->placeholder('Rating 1-5'),

                        FileUpload::make('foto')
                            ->image()
                            ->directory('barang')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->circular(), // Membuat tampilan foto jadi bulat (opsional)

                TextColumn::make('kode_barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_barang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('harga_barang')
                    ->label('Harga')
                    ->money('IDR') // Menggunakan formatter bawaan Filament (lebih aman)
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('stok')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('rating')
                    ->numeric(1)
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}