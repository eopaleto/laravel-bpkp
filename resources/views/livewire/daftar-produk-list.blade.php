<div>
    <div class="col-span-full bg-white rounded-xl p-3 md:p-4 shadow-sm mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div class="p-2 rounded">
                <input type="text" wire:model.live.debounce.1000ms="search" placeholder="Cari Produk..."
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
            </div>

            <div class="p-2 rounded">
                <select wire:model.live="kategori_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-gray-600 text-sm">
                    <option value="">-- Semua Kategori --</option>
                    @foreach (\App\Models\Kategori::all() as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-2 rounded">
                <select wire:model.live="sortBy"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-gray-600 text-sm">
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
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-4 lg:gap-6">
        @forelse ($this->produk as $item)
            <div
                class="bg-white hover:scale-105 rounded-lg md:rounded-2xl shadow-md hover:shadow-lg transition duration-300 p-4 md:p-4 flex flex-col">
                <div
                    class="w-full aspect-square overflow-hidden rounded-lg md:rounded-xl bg-gray-100 flex items-center justify-center">
                    @if ($item->avatar && file_exists(public_path('storage/' . $item->avatar)))
                        <img src="{{ asset('storage/' . $item->avatar) }}" alt="{{ $item->nama }}"
                            class="object-cover w-full h-full transition duration-300" />
                    @else
                        <span class="text-xs md:text-sm text-gray-500">Tidak ada gambar</span>
                    @endif
                </div>

                <div class="mt-2 md:mt-4 space-y-0.5 md:space-y-1">
                    <h3 class="text-xs md:text-base font-semibold text-gray-800 line-clamp-2 leading-tight">
                        {{ $item->nama }}
                    </h3>
                    <p class="text-xs md:text-sm text-gray-500">Stok: <span
                            class="font-medium text-gray-700">{{ $item->sisa }}</span></p>
                    <p class="text-sm md:text-lg font-bold text-amber-600">
                        Rp{{ number_format($item->hargajual, 0, ',', '.') }}
                    </p>
                </div>

                <button wire:click="addToCart('{{ $item->kode }}')"
                    class="mt-2 md:mt-4 w-full bg-amber-500 hover:bg-amber-600 text-white text-xs md:text-sm font-semibold px-2 md:px-4 py-1.5 md:py-2 rounded-lg md:rounded-xl transition duration-300 flex items-center justify-center gap-1 md:gap-2">
                    <span class="hidden md:inline">+</span> <span class="md:hidden">+</span> Keranjang
                </button>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center text-center py-16 px-4">
                <!-- Animated Image Icon -->
                <div class="relative mb-6 animate-bounce">
                    <img src="{{ asset('img/no-results.png') }}" 
                         alt="Produk tidak ditemukan" 
                         class="w-32 h-32 md:w-40 md:h-40 object-contain drop-shadow-lg">
                </div>

                <!-- Main Message -->
                <h3 class="text-2xl font-bold text-gray-800 mb-2 animate-fade-in">
                    Produk Tidak Ditemukan
                </h3>
                <p class="text-gray-600 text-base mb-6 max-w-md animate-fade-in-delay">
                    Maaf, produk yang Anda cari tidak tersedia. Coba kata kunci lain atau jelajahi kategori kami.
                </p>

                <!-- Suggestions -->
                <div class="bg-amber-50 rounded-xl p-6 max-w-md w-full animate-slide-up">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z">
                            </path>
                        </svg>
                        Tips Pencarian:
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-2 text-left">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">•</span>
                            <span>Periksa ejaan kata kunci</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">•</span>
                            <span>Gunakan kata kunci yang lebih umum</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">•</span>
                            <span>Coba filter kategori yang berbeda</span>
                        </li>
                    </ul>
                </div>

                <!-- Reset Button -->
                <button wire:click="$set('search', '')"
                    class="mt-6 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    Lihat Semua Produk
                </button>
            </div>

            <style>
                @keyframes fade-in {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes fade-in-delay {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @keyframes slide-up {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .animate-fade-in {
                    animation: fade-in 0.6s ease-out;
                }

                .animate-fade-in-delay {
                    animation: fade-in-delay 0.6s ease-out 0.2s both;
                }

                .animate-slide-up {
                    animation: slide-up 0.6s ease-out 0.4s both;
                }
            </style>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $this->produk->links() }}
    </div>
</div>
