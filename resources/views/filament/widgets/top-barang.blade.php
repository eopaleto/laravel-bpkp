<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-3">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">🏆</span>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Top 5 Barang Paling Laku
                </h3>
            </div>

            <div class="space-y-2">
                @foreach($this->getTopItems() as $item)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                {{ $item['rank'] === 1 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : '' }}
                                {{ $item['rank'] === 2 ? 'bg-green-50 text-green-600 dark:bg-green-900/50 dark:text-green-400' : '' }}
                                {{ $item['rank'] === 3 ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : '' }}
                                {{ $item['rank'] > 3 ? 'bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-500' : '' }}
                                font-bold text-sm">
                                {{ $item['rank'] }}
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate">
                                {{ $item['nama'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">
                                {{ number_format($item['jumlah']) }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Unit
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>