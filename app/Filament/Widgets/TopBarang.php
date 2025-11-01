<?php

namespace App\Filament\Widgets;

use App\Models\PermintaanItems;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TopBarang extends Widget
{
    protected static string $view = 'filament.widgets.top-barang';
    protected static ?string $pollingInterval = '60s';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 12,
            'sm' => 12,
            'md' => 6,
            'lg' => 6,
            'xl' => 6,
        ];
    }

    public function getTopItems(): array
    {
        return PermintaanItems::select('nama_barang', DB::raw('SUM(jumlah) as total_jumlah'))
            ->groupBy('nama_barang')
            ->orderByDesc('total_jumlah')
            ->limit(5)
            ->get()
            ->map(fn($item, $index) => [
                'rank' => $index + 1,
                'nama' => $item->nama_barang,
                'jumlah' => $item->total_jumlah,
            ])
            ->toArray();
    }
}
