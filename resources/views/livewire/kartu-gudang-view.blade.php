<div class="space-y-4 p-6 bg-white dark:bg-gray-950">
    <!-- Search & Filter -->
    <div class="flex gap-4 mb-6">
        <input 
            type="text" 
            placeholder="Cari nama barang..." 
            wire:model.live="search"
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-950 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500"
        />
        
        <select 
            wire:model.live="filterJenis"
            class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-950 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500"
        >
            <option value="">Semua Jenis</option>
            <option value="barang_keluar">Barang Keluar</option>
            <option value="barang_masuk">Barang Masuk</option>
        </select>
    </div>

    <!-- Grouped Table -->
    <div class="space-y-4">
        @forelse($groupedData as $namaBarang => $items)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900 shadow">
                <!-- Header Grup (Nama Barang) -->
                <button 
                    wire:click="toggleGroup('{{ $namaBarang }}')"
                    class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-between font-bold text-gray-800 dark:text-gray-100 transition"
                >
                    <span>{{ $namaBarang }}</span>
                    @if(count(array_filter($items, fn($item) => $item['tanggal'] !== null)) > 0)
                        <span class="text-sm bg-primary-600 dark:bg-primary-500 text-white rounded-full px-3 py-1">
                            {{ count(array_filter($items, fn($item) => $item['tanggal'] !== null)) }} transaksi
                        </span>
                    @else
                        <span class="text-sm bg-gray-400 dark:bg-gray-600 text-white rounded-full px-3 py-1">
                            Kosong
                        </span>
                    @endif
                </button>

                <!-- Detail Barang -->
                @if(in_array($namaBarang, $expandedGroups))
                    @php
                        $itemsWithData = array_filter($items, fn($item) => $item['tanggal'] !== null);
                    @endphp
                    
                    @if(count($itemsWithData) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Unit Kerja</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                                        <th class="px-6 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Jumlah</th>
                                        <th class="px-6 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Jenis</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($itemsWithData as $item)
                                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">
                                                {{ $item['unit_kerja'] ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">
                                                {{ $item['tanggal']->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $item['jumlah'] }}
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                @if($item['jenis'] === 'barang_keluar')
                                                    <span class="inline-block px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-full text-xs font-medium">
                                                        Barang Keluar
                                                    </span>
                                                @else
                                                    <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full text-xs font-medium">
                                                        Barang Masuk
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-4 bg-yellow-50 dark:bg-yellow-900/20 border-t border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400">
                            <p class="text-sm">Belum ada transaksi untuk barang ini</p>
                        </div>
                    @endif
                @endif
            </div>
        @empty
            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center text-gray-500 dark:text-gray-400">
                Tidak ada data kartu gudang
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan <span class="font-semibold">{{ count($groupedData) }}</span> dari <span class="font-semibold">{{ $totalItems }}</span> barang
            </div>
            
            <div class="flex items-center gap-1">
                <!-- Previous Button -->
                <button 
                    wire:click="previousPage"
                    @disabled($currentPage === 1)
                    class="relative inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- Page Numbers -->
                <div class="flex items-center gap-1">
                    @if($currentPage > 2)
                        <button 
                            wire:click="gotoPage(1)"
                            class="relative inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                        >
                            1
                        </button>
                        @if($currentPage > 3)
                            <span class="px-2 text-gray-600 dark:text-gray-400">...</span>
                        @endif
                    @endif

                    @for($i = max(1, $currentPage - 1); $i <= min($totalPages, $currentPage + 1); $i++)
                        @if($i === $currentPage)
                            <span class="bg-primary-600 dark:bg-primary-500 text-white relative inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-semibold">
                                {{ $i }}
                            </span>
                        @else
                            <button 
                                wire:click="gotoPage({{ $i }})"
                                class="relative inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                            >
                                {{ $i }}
                            </button>
                        @endif
                    @endfor
                    @if($currentPage < $totalPages - 1)
                        @if($currentPage < $totalPages - 2)
                            <span class="px-2 text-gray-600 dark:text-gray-400">...</span>
                        @endif
                        <button 
                            wire:click="gotoPage({{ $totalPages }})"
                            class="relative inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                        >
                            {{ $totalPages }}
                        </button>
                    @endif
                </div>

                <!-- Next Button -->
                <button 
                    wire:click="nextPage"
                    @disabled($currentPage === $totalPages)
                    class="relative inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
