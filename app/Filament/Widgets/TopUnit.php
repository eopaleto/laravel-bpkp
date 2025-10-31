<?php

namespace App\Filament\Widgets;

use App\Models\Permintaan;
use Filament\Widgets\ChartWidget;

class TopUnit extends ChartWidget
{
    protected static ?string $heading = '🏢 Top 5 Unit Kerja Paling Sering Meminta';
    protected static ?string $pollingInterval = '60s';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 12,
            'sm' => 12,
            'md' => 12,
            'lg' => 10,
        ];
    }

    protected function getData(): array
    {
        $data = Permintaan::with('user.unit')
            ->get()
            ->groupBy(fn($p) => $p->user?->unit?->name ?? 'Tidak diketahui')
            ->map(fn($items) => $items->count())
            ->sortDesc()
            ->take(5);

        // 🔹 Singkat label jika terlalu panjang
        $labels = $data->keys()->map(function ($label) {
            $words = explode(' ', $label);
            return count($words) > 3
                ? implode(' ', array_slice($words, 0, 3)) . '...'
                : $label;
        });

        $values = $data->values();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permintaan',
                    'data' => $values,
                    'backgroundColor' => [
                        '#0284c7', // sky-600
                        '#0ea5e9', // sky-500
                        '#38bdf8', // sky-400
                        '#7dd3fc', // sky-300
                        '#bae6fd', // sky-200
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
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'duration' => 400,
                'easing' => 'easeOutQuart',
            ],
            'interaction' => [
                'mode' => 'nearest',
                'intersect' => false,
                'axis' => 'y',
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
                        'display' => false
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
                    'grid' => ['display' => false],
                    'ticks' => [
                        'font' => ['size' => 13, 'weight' => '600'],
                        'maxRotation' => 0,
                        'autoSkip' => false,
                    ],
                ],
            ],
        ];
    }
}
