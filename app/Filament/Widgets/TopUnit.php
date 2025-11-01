<?php

namespace App\Filament\Widgets;

use App\Models\Permintaan;
use Filament\Widgets\Widget;

class TopUnit extends Widget
{
    protected static string $view = 'filament.widgets.top-unit';
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

    public function getTopUnits(): array
    {
        $data = Permintaan::with('user.unit')
            ->where('status', 'Disetujui')
            ->get()
            ->groupBy(fn($p) => $p->user?->unit?->name ?? 'Tidak diketahui')
            ->map(fn($items) => $items->count())
            ->sortDesc()
            ->take(5);

        return $data->map(fn($count, $unitName) => [
            'nama' => $unitName,
            'nama_pendek' => $this->shortenUnitName($unitName),
            'jumlah' => $count,
        ])->values()->map(fn($item, $index) => [
            'rank' => $index + 1,
            'nama' => $item['nama'],
            'nama_pendek' => $item['nama_pendek'],
            'jumlah' => $item['jumlah'],
        ])->toArray();
    }

    private function shortenUnitName(string $name): string
    {
        if (strlen($name) <= 75) {
            return $name;
        }

        $words = explode(' ', $name);
        $shortened = '';

        foreach ($words as $word) {
            if (strlen($shortened . ' ' . $word) > 75) {
                break;
            }
            $shortened .= ($shortened ? ' ' : '') . $word;
        }

        return $shortened . '...';
    }
}
