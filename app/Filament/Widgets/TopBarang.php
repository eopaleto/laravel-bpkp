<?php

namespace App\Filament\Widgets;

use Filament\Support\RawJs;
use App\Models\PermintaanItems;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopBarang extends ChartWidget
{
    protected static ?string $heading = '🏆 Top 5 Barang Paling Laku';
    protected static ?string $pollingInterval = '60s';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 12,
            'sm' => 12,
            'md' => 12,
            'lg' => 2,
        ];
    }

    protected function getData(): array
    {
        $topItems = PermintaanItems::select('nama_barang', DB::raw('SUM(jumlah) as total_jumlah'))
            ->groupBy('nama_barang')
            ->orderByDesc('total_jumlah')
            ->limit(5)
            ->get();

        $labels = $topItems->pluck('nama_barang');
        $data = $topItems->pluck('total_jumlah');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permintaan',
                    'data' => $data,
                    'backgroundColor' => [
                        '#16a34a',
                        '#22c55e',
                        '#4ade80',
                        '#86efac',
                        '#bbf7d0',
                    ],
                    'borderWidth' => 0,
                    'borderRadius' => 6,
                    'barThickness' => 20,
                    'maxBarThickness' => 30,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // inilah yang bikin grafik horizontal
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 400,
                'easing' => 'easeOutQuart',
            ],
            'interaction' => [
                'mode' => 'nearest', // lebih halus dari 'index'
                'intersect' => false,
                'axis' => 'y', // karena horizontal
            ],
            'hover' => [
                'mode' => 'nearest',
                'intersect' => false,
                'animationDuration' => 300,
            ],
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'nearest',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 2,
                        'maxTicksLimit' => 6,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 13,
                            'weight' => '600',
                        ],
                    ],
                ],
            ],
        ];
    }
}
