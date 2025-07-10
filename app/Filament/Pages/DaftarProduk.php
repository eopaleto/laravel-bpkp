<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use App\Models\KeranjangBarang;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;

class DaftarProduk extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $title = 'Daftar Produk';
    protected static ?string $navigationLabel = 'Daftar Produk';
    protected static string $view = 'filament.pages.daftar-produk';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?string $slug = 'daftar-produk';

    public string $sortBy = 'terbaru';

    public $produk; // pakai Collection agar bisa akses ->property

    public function mount(): void
    {
        $this->filterProduk();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sortBy')
                    ->label('Urutkan Berdasarkan')
                    ->options([
                        'terbaru' => 'Terbaru',
                        'harga_terendah' => 'Harga Termurah',
                        'harga_tertinggi' => 'Harga Termahal',
                        'stok_terbanyak' => 'Stok Terbanyak',
                        'nama_asc' => 'Nama (A-Z)',
                    ])
                    ->live()
                    ->afterStateUpdated(fn() => $this->filterProduk()),
            ]);
    }

    public function filterProduk(): void
    {
        $query = Barang::query();

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

        $this->produk = $query->get(); // KEMBALI KE KOLEKSI
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
