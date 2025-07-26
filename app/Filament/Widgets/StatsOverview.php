<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\LogBarangKeluar;
use App\Models\LogBarangMasuk;
use App\Models\Permintaan;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $alatTulis = Kategori::where('nama', 'Alat Tulis Kantor')->first();
        $alatKebersihan = Kategori::where('nama', 'Alat Kebersihan')->first();

        $barangMasukTotal = LogBarangMasuk::sum('jumlah');
        $barangMasukHariIni = LogBarangMasuk::whereDate('created_at', today())->sum('jumlah');
        $barangMasukKemarin = LogBarangMasuk::whereDate('created_at', today()->subDay())->sum('jumlah');
        $barangMasukIcon = $barangMasukHariIni >= $barangMasukKemarin
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';

        $barangKeluarTotal = LogBarangKeluar::sum('jumlah');
        $barangKeluarHariIni = LogBarangKeluar::whereDate('created_at', today())->sum('jumlah');
        $barangKeluarKemarin = LogBarangKeluar::whereDate('created_at', today()->subDay())->sum('jumlah');
        $barangKeluarIcon = $barangKeluarHariIni >= $barangKeluarKemarin
            ? 'heroicon-m-arrow-trending-up'
            : 'heroicon-m-arrow-trending-down';

        // Grafik 7 hari terakhir
        $barangMasukChart = collect(range(6, 0))->map(function ($daysAgo) {
            return LogBarangMasuk::whereDate('created_at', Carbon::today()->subDays($daysAgo))->sum('jumlah');
        })->toArray();

        $barangKeluarChart = collect(range(6, 0))->map(function ($daysAgo) {
            return LogBarangKeluar::whereDate('created_at', Carbon::today()->subDays($daysAgo))->sum('jumlah');
        })->toArray();

        return [
            Stat::make('Total Barang', Barang::count())
                ->description("Total Barang")
                ->icon('heroicon-o-cube')
                ->color('primary'),
            Stat::make('Barang Masuk', $barangMasukTotal)
                ->description("{$barangMasukHariIni} Barang masuk hari ini")
                ->descriptionIcon($barangMasukIcon)
                ->icon('heroicon-o-arrow-down-tray')
                ->chart($barangMasukChart)
                ->color('success'),

            Stat::make('Barang Keluar', $barangKeluarTotal)
                ->description("{$barangKeluarHariIni} Barang keluar hari ini")
                ->descriptionIcon($barangKeluarIcon)
                ->icon('heroicon-o-arrow-up-tray')
                ->chart($barangKeluarChart)
                ->color('warning'),
            Stat::make('Jumlah Admin', User::role('Admin')->count())
                ->description('Pengguna Dengan Role Admin')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            Stat::make('Jumlah Permintaan Barang', Permintaan::where('status', 'Disetujui')->count())
                ->description('Disetujui')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            Stat::make('Jumlah Permintaan Barang', Permintaan::where('status', 'Ditolak')->count())
                ->description('Ditolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            Stat::make('Jumlah Permintaan Barang', Permintaan::where('status', 'Menunggu')->count())
                ->description('Menunggu Persetujuan')
                ->icon('heroicon-o-clock')
                ->color('info'),
            Stat::make('Alat Tulis Kantor', $alatTulis?->barangs()->count() ?? 0)
                ->description('Jumlah barang kategori Alat Tulis Kantor')
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),
            Stat::make('Alat Kebersihan', $alatKebersihan?->barangs()->count() ?? 0)
                ->description('Jumlah barang kategori Alat Kebersihan')
                ->icon('heroicon-o-sparkles')
                ->color('success'),
        ];
    }

    public function getColumns(): int
    {
        return 3;
    }

    public function getColumnSpan(): array|int|string
    {
        return '12';
    }
}
