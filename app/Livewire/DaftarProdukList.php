<?php

namespace App\Livewire;

use App\Models\Barang;
use Livewire\Component;
use App\Models\Kategori;
use Livewire\WithPagination;
use App\Models\KeranjangBarang;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class DaftarProdukList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'terbaru';
    public string|int|null $kategori_id = null;

    public function getKategori()
    {
        return Kategori::orderBy('nama')->get();
    }

    public function addToCart($kode)
    {
        $userId = Auth::id();
        $periodeTahun = session('periode_tahun') ?? auth()->user()->periode_tahun;

        $existing = KeranjangBarang::where('user_id', $userId)
            ->where('kode', $kode)
            ->where('periode_tahun', $periodeTahun)
            ->first();

        if ($existing) {
            $existing->increment('jumlah');
        } else {
            KeranjangBarang::create([
                'user_id' => $userId,
                'kode' => $kode,
                'jumlah' => 1,
                'periode_tahun' => $periodeTahun,
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

    #[Computed]
    public function produk()
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

        return $query->paginate(12);
    }

    public function render()
    {
        return view('livewire.daftar-produk-list');
    }
}
