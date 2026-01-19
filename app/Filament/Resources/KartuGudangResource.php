<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KartuGudangResource\Pages;
use App\Filament\Resources\KartuGudangResource\RelationManagers;
use App\Models\LogBarangKeluar;
use App\Models\LogBarangMasuk;

class KartuGudangResource extends Resource
{
    // No model - this is a view-only report resource
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Menu Khusus';

    protected static ?string $slug = 'KartuGudang';

    protected static ?string $navigationLabel = 'Kartu Gudang';

    public static function getNavigationBadge(): ?string
    {
        $totalTransactions = LogBarangKeluar::count() + LogBarangMasuk::count();
        return $totalTransactions > 0 ? (string) $totalTransactions : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ]);
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

        return $user?->hasRole('SuperAdmin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKartuGudangs::route('/'),
        ];
    }
}
