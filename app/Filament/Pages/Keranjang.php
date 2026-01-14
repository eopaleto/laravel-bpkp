<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use App\Models\KeranjangBarang;
use Illuminate\Support\Facades\Auth;

class Keranjang extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $title = 'Keranjang Belanja';
    protected static string $view = 'filament.pages.keranjang';
    public $items;
    public $total;

    public function mount()
    {
        $this->items = KeranjangBarang::with('barang')
            ->where('user_id', Auth::id())
            ->where('periode_tahun', session('periode_tahun') ?? auth()->user()->periode_tahun)
            ->get();

        $this->total = $this->items->sum(fn($item) => $item->jumlah * ($item->barang->hargajual ?? 0));
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) KeranjangBarang::where('user_id', Auth::id())
            ->where('periode_tahun', session('periode_tahun') ?? auth()->user()->periode_tahun)
            ->count();
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('User');
    }
}
