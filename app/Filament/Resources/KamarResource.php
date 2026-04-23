<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KamarResource\Pages;
use App\Filament\Resources\KamarResource\RelationManagers;
use App\Models\Kamar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; //kita menggunakan textinput
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;


class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    TextInput::make('id')
                        ->required()
                        ->placeholder('Masukkan id')
                    ,
                    TextInput::make('no_kamar')
                        ->required()
                        ->placeholder('Masukkan nomer kamar')
                    ,
                    TextInput::make('nama_kamar')
                        ->required()
                        ->placeholder('Masukkan nama kamar')
                    ,
                    TextInput::make('Lantai_kamar')
                        ->required()
                        ->placeholder('Masukkan lantai kamar')
                    ,
                    FileUpload::make('foto_kamar')
                    ->directory('foto')
                    ->required()
                    ,
                    TextInput::make('harga_kamar')
                    ->required()
                    ->minValue(0) // Nilai minimal 0 (opsional jika tidak ingin ada harga negatif)
                    ->reactive() // Menjadikan input reaktif terhadap perubahan
                    ->extraAttributes(['id' => 'harga-kamar']) // Tambahkan ID untuk pengikatan JavaScript
                    ->placeholder('Masukkan harga kamar') // Placeholder untuk membantu pengguna
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('harga_kamar', number_format((int) str_replace('.', '', $state), 0, ',', '.'))
                      )
                    ,
                    TextInput::make('Lantai_kamar')
                        ->required()
                        ->placeholder('Masukkan lantai kamar')
                    ,
                    TextInput::make('status_kamar')
                        ->required()
                        ->placeholder('Masukkan lantai kamar')
                    ,

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('no_kamar'),
                TextColumn::make('nama_kamar'),
                TextColumn::make('Lantai_kamar'),
                ImageColumn::make('foto_kamar'), 
                TextColumn::make('harga_kamar'),
                TextColumn::make('status_kamar'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKamars::route('/'),
            'create' => Pages\CreateKamar::route('/create'),
            'edit' => Pages\EditKamar::route('/{record}/edit'),
        ];
    }
}
