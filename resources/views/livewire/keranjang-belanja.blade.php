<div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($items->isEmpty())
        <div
            class="flex flex-col items-center justify-center py-40 text-center space-y-4 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
            <x-heroicon-o-shopping-cart class="w-14 h-14 text-gray-400 dark:text-gray-500" />
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Keranjang masih kosong nih ☹</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Yuk, mulai pilih produk pilihanmu sekarang!</p>
            <a href="{{ route('filament.admin.pages.daftar-produk') }}"
                class="inline-block mt-4 bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-700 text-white font-semibold px-6 py-2 rounded-lg transition shadow-sm">
                Ayo Pilih Produk
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                <div
                    class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <!-- Product Info -->
                    <div class="flex items-center gap-3 flex-1 min-w-0 overflow-hidden">
                        <div class="w-14 h-14 min-w-[3.5rem] rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-gray-600">
                            @if ($item->barang->avatar && file_exists(public_path('storage/' . $item->barang->avatar)))
                                <img src="{{ asset('storage/' . $item->barang->avatar) }}"
                                    alt="{{ $item->barang->nama }}"
                                    class="w-full h-full object-cover" />
                            @else
                                <svg class="w-8 h-8 text-gray-400 dark:text-grey-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 max-w-[45%] sm:max-w-[50%]">
                            <h3 class="font-semibold text-sm text-gray-900 dark:text-white leading-tight line-clamp-2 break-words">
                                {{ $item->barang->nama }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-white mt-0.5 truncate">
                                Rp{{ number_format($item->barang->hargajual, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center w-28 gap-2 justify-center self-center">
                        <button wire:click="kurang({{ $item->id }})"
                            class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold flex items-center justify-center transition">
                            −
                        </button>
                        <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $item->jumlah }}</span>
                        <button wire:click="tambah({{ $item->id }})"
                            class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold flex items-center justify-center transition">
                            +
                        </button>
                    </div>

                    <!-- Price & Delete -->
                    <div class="text-right w-32">
                        <p class="text-gray-900 dark:text-white font-bold text-sm">
                            Rp{{ number_format($item->jumlah * $item->barang->hargajual, 0, ',', '.') }}
                        </p>
                        <button wire:click="hapus({{ $item->id }})"
                            class="inline-flex items-center gap-1 text-red-500 dark:text-red-400 text-xs hover:underline mt-1 transition">
                            <x-heroicon-o-trash class="w-4 h-4" />
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2 p-4 text-right">
            <p class="text-lg font-semibold mb-2">Total: Rp{{ number_format($total, 0, ',', '.') }}</p>
            <button wire:click="checkout"
                class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-2 rounded-lg transition inline-flex gap-2">
                <x-heroicon-o-credit-card class="w-5 h-5" />
                Checkout
            </button>
        </div>
    @endif
</div>
