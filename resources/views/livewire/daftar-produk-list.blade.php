<div>
    <div class="col-span-full bg-white rounded-xl p-4 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-2 rounded">
                <input type="text" wire:model.debounce.500ms="search" placeholder="Cari Produk..."
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="p-2 rounded">
                <select wire:model="kategori_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-gray-600">
                    <option value="">-- Semua Kategori --</option>
                    @foreach (\App\Models\Kategori::all() as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-2 rounded">
                <select wire:model="sortBy"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-gray-600">
                    <option value="terbaru">Terbaru</option>
                    <option value="harga_terendah">Harga Termurah</option>
                    <option value="harga_tertinggi">Harga Termahal</option>
                    <option value="stok_terbanyak">Stok Terbanyak</option>
                    <option value="nama_asc">Nama (A-Z)</option>
                </select>
            </div>
        </div>
    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    <div class="text-xs text-gray-500 p-4">
        Search: {{ $search }} |
        Sort: {{ $sortBy }} |
        Kategori ID: {{ $kategori_id }}
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($this->produk as $barang)
            <div
                class="bg-white hover:scale-105 rounded-2xl shadow-md hover:shadow-lg transition duration-300 p-4 flex flex-col">
                <div
                    class="w-full aspect-square overflow-hidden rounded-xl bg-gray-100 flex items-center justify-center">
                    <img src="{{ asset('storage/' . $barang->avatar) }}" alt="{{ $barang->nama }}"
                        class="object-cover w-full h-full transition duration-300" />
                </div>

                <div class="mt-4 space-y-1">
                    <h3 class="text-base font-semibold text-gray-800 truncate">
                        {{ $barang->nama }}
                    </h3>
                    <p class="text-sm text-gray-500">Stok: <span
                            class="font-medium text-gray-700">{{ $barang->sisa }}</span></p>
                    <p class="text-lg font-bold text-amber-600">Rp{{ number_format($barang->hargajual, 0, ',', '.') }}
                    </p>
                </div>

                <button wire:click="addToCart('{{ $barang->kode }}')"
                    class="mt-4 w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition duration-300 flex items-center justify-center gap-2">
                    + Tambah ke Keranjang
                </button>
            </div>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $this->produk->links() }}
    </div>
</div>
