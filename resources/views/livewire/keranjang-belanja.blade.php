<div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($items->isEmpty())
        <div class="flex flex-col items-center justify-center py-40 text-center space-y-4 bg-white rounded-xl shadow">
            <x-heroicon-o-shopping-cart class="w-14 h-14 text-gray-400" />
            <p class="text-lg font-semibold text-gray-600">Keranjang masih kosong nih ☹</p>
            <p class="text-sm text-gray-500">Yuk, mulai pilih produk pilihanmu sekarang!</p>
            <a href="{{ route('filament.admin.pages.daftar-produk') }}"
                class="inline-block mt-4 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                Ayo Pilih Produk
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                <div class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('storage/' . $item->barang->avatar) }}"
                            class="w-14 h-14 rounded-lg object-cover" />
                        <div>
                            <h3 class="font-medium text-sm">{{ $item->barang->nama }}</h3>
                            <p class="text-xs text-gray-500">Rp{{ number_format($item->barang->hargajual, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="kurang({{ $item->id }})"
                            class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-xs font-bold flex items-center justify-center">
                            −
                        </button>
                        <span class="text-sm">{{ $item->jumlah }}</span>
                        <button wire:click="tambah({{ $item->id }})"
                            class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 text-xs font-bold flex items-center justify-center">
                            +
                        </button>
                    </div>

                    <div class="text-right w-32">
                        <p class="text-black font-bold text-sm">
                            Rp{{ number_format($item->jumlah * $item->barang->hargajual, 0, ',', '.') }}
                        </p>
                        <button wire:click="hapus({{ $item->id }})"
                            class="inline-flex items-center gap-1 text-red-500 text-xs hover:underline mt-1">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 p-4 text-right">
            <p class="text-lg font-semibold mb-2">Total: Rp{{ number_format($total, 0, ',', '.') }}</p>
            <button wire:click="checkout"
                class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-2 rounded-lg transition inline-flex gap-2">
                <x-heroicon-o-credit-card class="w-5 h-5" />
                Checkout
            </button>
        </div>
    @endif
</div>
