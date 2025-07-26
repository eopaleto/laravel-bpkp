<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogBarangMasuk;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LogBarangMasukResource\Pages;
use App\Filament\Resources\LogBarangMasukResource\RelationManagers;
use App\Filament\Resources\LogBarangMasukResource\Pages\ListLogBarangMasuks;

class LogBarangMasukResource extends Resource
{
    protected static ?string $model = LogBarangMasuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Menu Khusus';
    protected static ?string $slug = 'LogBarangMasuk';
    public static function getNavigationBadge(): ?string
    {
        return (string) LogBarangMasuk::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_barang')->label('Kode')->alignCenter(),
                TextColumn::make('barang.nama')->label('Nama Barang')->alignCenter(),
                TextColumn::make('unit_kerja.name')->label('Unit Kerja')->alignCenter(),
                TextColumn::make('jumlah')->alignCenter(),
                TextColumn::make('created_at')->label('Tanggal')->dateTime()->alignCenter(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Admin');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogBarangMasuks::route('/'),
        ];
    }
}
