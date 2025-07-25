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

    public string $search = '';
    public string $sortBy = 'terbaru';
    public string|int|null $kategori_id = null;

    public function mount(): void
    {
        $this->resetPage();
    }

    public function updated($field): void
    {
        if (in_array($field, ['search', 'sortBy', 'kategori_id'])) {
            $this->resetPage();
        }
    }

    public function getKategori()
    {
        return Kategori::orderBy('nama')->get();
    }

    public function getProdukProperty()
    {
        $query = Barang::query();

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        if ($this->kategori_id) {
            $query->where('kategori_id', $this->kategori_id);
        }

        switch ($this->sortBy) {
            case 'harga_terendah':
                $query->orderBy('hargajual', 'asc');
                break;
            case 'harga_tertinggi':
                $query->orderBy('hargajual', 'desc');
                break;
            case 'stok_terbanyak':
                $query->orderBy('sisa', 'desc');
                break;
            case 'nama_asc':
                $query->orderBy('nama', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        return $query->paginate(8);
    }

    public function addToCart($kode): void
    {
        $userId = Auth::id();

        $existing = KeranjangBarang::where('user_id', $userId)
            ->where('kode', $kode)
            ->first();

        if ($existing) {
            $existing->increment('jumlah');
        } else {
            KeranjangBarang::create([
                'user_id' => $userId,
                'kode' => $kode,
                'jumlah' => 1,
            ]);
        }

        Notification::make()
            ->title('Berhasil')
            ->body('Barang ditambahkan ke keranjang.')
            ->success()
            ->actions([
                Action::make('lihatKeranjang')
                    ->label('🛒 Lihat Keranjang')
                    ->url(route('filament.admin.pages.keranjang'))
                    ->openUrlInNewTab(),
            ])
            ->send();
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('User');
    }
}
