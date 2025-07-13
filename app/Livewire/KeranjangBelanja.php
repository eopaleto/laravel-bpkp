<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Permintaan;
use App\Models\KeranjangBarang;
use App\Models\PermintaanItems;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class KeranjangBelanja extends Component
{
    public $items;
    public $total;
    protected $listeners = ['refreshKeranjang' => '$refresh'];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $this->items = KeranjangBarang::with('barang')
            ->where('user_id', Auth::id())
            ->get();

        $this->total = $this->items->sum(fn($item) => $item->jumlah * ($item->barang->hargajual ?? 0));
    }

    public function tambah($id)
    {
        $item = KeranjangBarang::find($id);
        if ($item) {
            $item->increment('jumlah');
            $this->loadItems();
        }
    }

    public function kurang($id)
    {
        $item = KeranjangBarang::find($id);
        if ($item && $item->jumlah > 1) {
            $item->decrement('jumlah');
            $this->loadItems();
        }
    }

    public function hapus($id)
    {
        $item = KeranjangBarang::find($id);
        if ($item) {
            $item->delete();
            $this->loadItems();
        }
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $items = KeranjangBarang::with('barang')->where('user_id', Auth::id())->get();

        if ($items->isEmpty()) {
            Notification::make()
                ->title('Keranjang kosong')
                ->warning()
                ->send();
            return;
        }

        $total = 0;

        foreach ($items as $item) {
            $total += $item->jumlah * $item->barang->hargajual;
        }

        $permintaan = Permintaan::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'status' => 'Menunggu',
        ]);

        foreach ($items as $item) {
            PermintaanItems::create([
                'permintaan_checkout_id' => $permintaan->id,
                'nama_barang' => $item->barang->nama,
                'jumlah' => $item->jumlah,
                'harga_satuan' => $item->barang->hargajual,
                'subtotal' => $item->jumlah * $item->barang->hargajual,
            ]);
        }

        KeranjangBarang::where('user_id', Auth::id())->delete();

        Notification::make()
            ->title('Checkout berhasil')
            ->body('Permintaan telah dikirim. Menunggu persetujuan admin.')
            ->success()
            ->persistent()
            ->send();

        $this->loadItems();
    }

    public function render()
    {
        return view('livewire.keranjang-belanja');
    }
}
