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

    #[Computed]
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

    public function getKategori()
    {
        return Kategori::orderBy('nama')->get();
    }

    public function addToCart($kode)
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

    public function updated($field)
    {
        if (in_array($field, ['search', 'sortBy', 'kategori_id'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.daftar-produk-list');
    }
}
