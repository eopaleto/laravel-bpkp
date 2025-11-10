<?php

namespace App\Filament\Resources;

use Filament\Tables\Table;
use App\Models\LogBarangKeluar;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LogBarangKeluarResource\Pages;
use Filament\Tables\Columns\ActionsColumn;

class LogBarangKeluarResource extends Resource
{
    protected static ?string $model = LogBarangKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Menu Khusus';
    protected static ?string $slug = 'LogBarangKeluar';

    public static function getNavigationBadge(): ?string
    {
        return (string) LogBarangKeluar::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama')->label('Nama Barang')->alignCenter(),
                TextColumn::make('kode_barang')->label('Kode Barang')->searchable()->alignCenter(),
            ])
            ->actions([
                Action::make('viewDetail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Barang Keluar')
                    ->modalSubheading(
                        fn($record) =>
                        'Nama Barang : ' . $record->barang->nama . ' | Kode Barang : ' . $record->kode_barang
                    )
                    ->modalButton('Tutup')
                    ->action(fn() => null)
                    ->modalContent(function ($record) {
                        $logs = LogBarangKeluar::with(['user', 'unit_kerja'])
                            ->where('kode_barang', $record->kode_barang)
                            ->get();

                        $html = '
    <div class="overflow-x-auto">
        <table class="min-w-full table-fixed border border-gray-300 dark:border-gray-700 text-sm rounded-lg overflow-hidden shadow-sm">
            <thead class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <tr>
                    <th class="w-1/5 border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center font-semibold">Tanggal</th>
                    <th class="w-1/5 border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center font-semibold">Unit</th>
                    <th class="w-1/5 border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center font-semibold">Nama Pengambil</th>
                    <th class="w-1/5 border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center font-semibold">Jumlah</th>
                    <th class="w-1/5 border-b border-gray-300 dark:border-gray-600 px-4 py-2 text-center font-semibold">Sisa Stok Saat Itu</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100">
    ';

                        if ($logs->isEmpty()) {
                            $html .= '
            <tr>
                <td colspan="5" class="px-4 py-4 text-center text-gray-600 dark:text-gray-300">
                    Tidak ada log barang keluar.
                </td>
            </tr>
        ';
                        } else {
                            foreach ($logs as $index => $log) {
                                $rowBg = $index % 2 === 0
                                    ? 'bg-gray-50 dark:bg-gray-800'
                                    : 'bg-gray-100 dark:bg-gray-900';

                                $html .= '
                <tr class="' . $rowBg . ' hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                    <td class="w-1/5 border-t border-gray-200 dark:border-gray-700 px-4 py-2 text-center truncate">' . e($log->created_at->format('d-m-Y H:i')) . '</td>
                    <td class="w-1/5 border-t border-gray-200 dark:border-gray-700 px-4 py-2 text-center truncate">' . e($log->unit_kerja->name ?? '-') . '</td>
                    <td class="w-1/5 border-t border-gray-200 dark:border-gray-700 px-4 py-2 text-center truncate">' . e($log->user->name ?? 'Tidak ada user') . '</td>
                    <td class="w-1/5 border-t border-gray-200 dark:border-gray-700 px-4 py-2 text-center font-semibold">' . e($log->jumlah) . '</td>
                    <td class="w-1/5 border-t border-gray-200 dark:border-gray-700 px-4 py-2 text-center">' . e($log->sisa_stok_saat_itu) . '</td>
                </tr>
            ';
                            }
                        }

                        $html .= '
            </tbody>
        </table>
    </div>
    ';

                        return new HtmlString($html);
                    })
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return LogBarangKeluar::query()
            ->select(DB::raw('MIN(id) as id'), 'kode_barang')
            ->groupBy('kode_barang')
            ->with('barang')
            ->orderBy('id');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('SuperAdmin');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogBarangKeluars::route('/'),
        ];
    }
}
