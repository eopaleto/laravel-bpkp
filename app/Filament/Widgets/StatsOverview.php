<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Permintaan;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $alatTulis = Kategori::where('nama', 'Alat Tulis Kantor')->first();
        $alatKebersihan = Kategori::where('nama', 'Alat Kebersihan')->first();

        return [
            Stat::make('Total Semua Users', User::count())
                ->description('Pengguna Terdaftar')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Jumlah Users', User::role('User')->count())
                ->description('Pengguna Dengan Role User')
                ->icon('heroicon-o-users')
                ->color('primary'),
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
            Stat::make('Total Barang', Barang::count())
                ->description('Jumlah semua barang')
                ->icon('heroicon-o-cube')
                ->color('primary'),
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
