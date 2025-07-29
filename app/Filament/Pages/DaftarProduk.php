<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\Kategori;
use Livewire\WithPagination;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\KeranjangBarang;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class DaftarProduk extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $title = 'Daftar Produk';
    protected static ?string $navigationLabel = 'Daftar Produk';
    protected static string $view = 'filament.pages.daftar-produk';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $slug = 'daftar-produk';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('User');
    }
}
