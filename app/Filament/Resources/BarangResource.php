<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Imports\BarangImport;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\BarangResource\Pages;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $modelLabel = 'Barang';
    protected static ?string $navigationLabel = 'Barang';
    protected static ?string $pluralModelLabel = 'Barang';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $slug = 'barang';

    public static function getNavigationBadge(): ?string
    {
        return (string) Barang::count();
    }

    public static function form(Form $form): Form
    {
        /** @var User|null $user */
        $user = Auth::user();
        $isAdmin = $user?->hasRole('Admin');
        $isSuperAdmin = $user?->hasRole('SuperAdmin');

        return $form->schema([
            Section::make('Informasi Produk')
                ->description('Data utama produk seperti nama, kode, kategori, dan satuan.')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Barang')
                        ->maxLength(200)
                        ->required()
                        ->disabled($isAdmin),
                    
                    TextInput::make('kode')
                        ->label('Kode')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->disabled($isAdmin)
                        ->validationMessages([
                            'unique' => 'Kode barang ini sudah digunakan!',
                        ]),
                    
                    TextInput::make('sku')
                        ->label('SKU')
                        ->required()
                        ->maxLength(20)
                        ->disabled($isAdmin),

                    Select::make('kategori_id')
                        ->label('Kategori')
                        ->relationship('kategori', 'nama')
                        ->required()
                        ->disabled($isAdmin),

                    TextInput::make('satuan')
                        ->label('Satuan')
                        ->required()
                        ->maxLength(20)
                        ->disabled($isAdmin),
                    
                    TextInput::make('barcode')
                        ->label('Barcode')
                        ->required()
                        ->maxLength(50)
                        ->disabled($isAdmin),
                ])
                ->columns(3),

            Section::make('Harga & Stok')
                ->description('Data pembelian, penjualan, dan stok barang.')
                ->schema([
                    TextInput::make('hargabeli')
                        ->label('Harga Beli')
                        ->numeric()
                        ->required(),
                    
                    TextInput::make('hargajual')
                        ->label('Harga Jual')
                        ->numeric()
                        ->required(),
                    
                    TextInput::make('stokmin')
                        ->label('Stok Min')
                        ->numeric()
                        ->required(),

                    TextInput::make('terbeli')
                        ->label('Terbeli')
                        ->required()
                        ->numeric(),
                    
                    TextInput::make('terjual')
                        ->label('Terjual')
                        ->required()
                        ->numeric(),
                    
                    TextInput::make('sisa')
                        ->label('Sisa')
                        ->required()
                        ->numeric(),
                ])
                ->columns(3),

            Section::make('Spesifikasi & Detail Lainnya')
                ->description('Warna, ukuran, brand, lokasi, dan deskripsi tambahan.')
                ->schema([
                    TextInput::make('warna')
                        ->label('Warna')
                        ->maxLength(20)
                        ->disabled($isAdmin),
                    
                    TextInput::make('ukuran')
                        ->label('Ukuran')
                        ->maxLength(10)
                        ->disabled($isAdmin),
                    
                    TextInput::make('lokasi')
                        ->label('Lokasi')
                        ->maxLength(50)
                        ->disabled($isAdmin),

                    TextInput::make('brand')
                        ->label('Brand')
                        ->maxLength(100)
                        ->disabled($isAdmin),
                    
                    TextInput::make('keterangan')
                        ->label('Keterangan')
                        ->maxLength(200)
                        ->columnSpan(2)
                        ->disabled($isAdmin),
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
                        ->visibility('public')
                        ->disabled($isAdmin),

                    DatePicker::make('expired')
                        ->label('Expired')
                        ->disabled($isAdmin),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        /** @var User|null $user */
        $user = Auth::user();
        $isSuperAdmin = $user?->hasRole('SuperAdmin');

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
                TextColumn::make('sisa')->label('Sisa'),
                TextColumn::make('hargajual')->label('Harga Jual')->money('IDR'),
                TextColumn::make('expired')->label('Expired')->date(),
            ])
            ->headerActions(
                $isSuperAdmin ? [
                    Action::make('Import Excel')
                        ->form([
                            FileUpload::make('file')
                                ->label('Upload Excel (.xlsx)')
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            Excel::import(new BarangImport, $data['file']);
                            Notification::make()
                                ->title('Import berhasil!')
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Impor Data Barang dari Excel')
                        ->modalButton('Import'),
                ] : []
            )
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
                Tables\Actions\ViewAction::make()->color('success'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn() => $isSuperAdmin),
            ])
            ->bulkActions(
                $isSuperAdmin ? [
                    Tables\Actions\DeleteBulkAction::make(),
                ] : []
            );
    }

    public static function canCreate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->hasRole('SuperAdmin') ?? false;
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole(['SuperAdmin', 'Admin']);
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
            'view' => Pages\ViewBarang::route('/{record}'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}