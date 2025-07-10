<x-filament::page class="max-w-full px-2">
    <div class="mb-1">
        {{ $this->form }}
    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (session('success'))
        <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($this->produk as $barang)
            <div
                class="bg-white hover:scale-105 rounded-2xl shadow-md hover:shadow-lg transition duration-300 p-4 flex flex-col">

                <div
                    class="w-full aspect-square overflow-hidden rounded-xl bg-gray-100 flex items-center justify-center">
                    <img src="{{ asset('storage/' . $barang->avatar) }}" alt="{{ $barang->nama }}"
                        class="object-cover w-full h-full transition duration-300">
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
</x-filament::page>
