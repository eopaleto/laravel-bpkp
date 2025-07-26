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
                        'Nama Barang: ' . $record->barang->nama . ' | Kode Barang: ' . $record->kode_barang
                    )
                    ->modalButton('Tutup')
                    ->action(fn() => null)
                    ->modalContent(function ($record) {
                        $logs = LogBarangKeluar::with(['user', 'unit_kerja'])
                            ->where('kode_barang', $record->kode_barang)
                            ->get();

                        $html = '<div class="overflow-x-auto">';
                        $html .= '<table class="min-w-full border border-gray-300 text-sm">';
                        $html .= '<thead class="bg-gray-100">';
                        $html .= '<tr>';
                        $html .= '<th class="border px-4 py-2 text-center">Tanggal</th>';
                        $html .= '<th class="border px-4 py-2 text-center">Unit</th>';
                        $html .= '<th class="border px-4 py-2 text-center">Nama Pengambil</th>';
                        $html .= '<th class="border px-4 py-2 text-center">Jumlah</th>';
                        $html .= '<th class="border px-4 py-2 text-center">Sisa Stok Saat Itu</th>';
                        $html .= '</tr>';
                        $html .= '</thead><tbody>';

                        foreach ($logs as $log) {
                            $html .= '<tr>';
                            $html .= '<td class="border px-4 py-2 text-center">' . $log->created_at->format('d-m-Y H:i') . '</td>';
                            $html .= '<td class="border px-4 py-2 text-center">' . ($log->unit_kerja->name ?? '-') . '</td>';
                            $html .= '<td class="border px-4 py-2 text-center">' . ($log->user->name ?? 'Tidak ada user') . '</td>';
                            $html .= '<td class="border px-4 py-2 text-center">' . $log->jumlah . '</td>';
                            $html .= '<td class="border px-4 py-2 text-center">' . $log->sisa_stok_saat_itu . '</td>';
                            $html .= '</tr>';
                        }

                        $html .= '</tbody></table></div>';

                        return new HtmlString($html);
                    }),
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

        return $user?->hasRole('Admin');
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
