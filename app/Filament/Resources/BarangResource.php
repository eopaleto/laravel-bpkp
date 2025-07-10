<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Barang;
use App\Models\Kategori;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\BarangResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangResource\RelationManagers;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $modelLabel = 'Barang';
    protected static ?string $navigationLabel = 'Barang';
    protected static ?string $pluralModelLabel = 'Barang';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $slug = 'barang';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Produk')
                ->description('Data utama produk seperti nama, kode, kategori, dan satuan.')
                ->schema([
                    TextInput::make('nama')->label('Nama Barang')->maxLength(200)->required(),
                    TextInput::make('kode')
                        ->label('Kode')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->validationMessages([
                            'unique' => 'Kode barang ini sudah digunakan!',
                        ]),
                    TextInput::make('sku')->label('SKU')->required()->maxLength(20),

                    Select::make('kategori_id')
                        ->label('Kategori')
                        ->relationship('kategori', 'nama')
                        ->required(),

                    TextInput::make('satuan')->label('Satuan')->required()->maxLength(20),
                    TextInput::make('barcode')->label('Barcode')->required()->maxLength(50),
                ])
                ->columns(3),

            Section::make('Harga & Stok')
                ->description('Data pembelian, penjualan, dan stok barang.')
                ->schema([
                    TextInput::make('hargabeli')->label('Harga Beli')->numeric()->required(),
                    TextInput::make('hargajual')->label('Harga Jual')->numeric()->required(),
                    TextInput::make('stokmin')->label('Stok Min')->numeric()->required(),

                    TextInput::make('terbeli')->label('Terbeli')->numeric(),
                    TextInput::make('terjual')->label('Terjual')->numeric(),
                    TextInput::make('sisa')->label('Sisa')->numeric(),
                ])
                ->columns(3),

            Section::make('Spesifikasi & Detail Lainnya')
                ->description('Warna, ukuran, brand, lokasi, dan deskripsi tambahan.')
                ->schema([
                    TextInput::make('warna')->label('Warna')->required()->maxLength(20),
                    TextInput::make('ukuran')->label('Ukuran')->required()->maxLength(10),
                    TextInput::make('lokasi')->label('Lokasi')->required()->maxLength(50),

                    TextInput::make('brand')->label('Brand')->required()->maxLength(100),
                    TextInput::make('keterangan')->label('Keterangan')->maxLength(200)->columnSpan(2),
                ])
                ->columns(3),

            Section::make('Gambar & Tanggal')
                ->description('Unggah gambar produk dan atur tanggal expired jika diperlukan.')
                ->schema([
                    FileUpload::make('avatar')
                        ->label('Gambar')
                        ->directory('barang')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->maxSize(2048)
                        ->visibility('public'),

                    DatePicker::make('expired')->label('Expired')->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular()
                    ->size(50),
                TextColumn::make('kode')->label('Kode')->searchable()->sortable(),
                TextColumn::make('sku')->label('SKU')->searchable()->sortable(),
                TextColumn::make('nama')->label('Nama Barang')->searchable(),
                TextColumn::make('kategori.nama')
                    ->label('Kategori'),
                TextColumn::make('stokmin')->label('Stok Min'),
                TextColumn::make('sisa')->label('Sisa'),
                TextColumn::make('hargajual')->label('Harga Jual')->money('IDR'),
                TextColumn::make('expired')->label('Expired')->date(),
            ])
            ->filters([
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),

                Filter::make('lokasi')
                    ->form([
                        TextInput::make('lokasi')->label('Lokasi'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['lokasi'], fn($q) => $q->where('lokasi', 'like', '%' . $data['lokasi'] . '%'));
                    }),

                TernaryFilter::make('sisa')
                    ->label('Stok Kosong')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak')
                    ->queries(
                        true: fn($query) => $query->where('sisa', '<=', 0),
                        false: fn($query) => $query->where('sisa', '>', 0),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Admin');
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
