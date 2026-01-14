<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Form;
use App\Models\Permintaan;
use Filament\Tables\Table;
use App\Models\LogPermintaan;
use App\Models\LogBarangMasuk;
use App\Models\LogBarangKeluar;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PermintaanResource\Pages;
use App\Filament\Resources\PermintaanResource\Pages\EditPermintaan;
use App\Filament\Resources\PermintaanResource\Pages\ListPermintaans;

class PermintaanResource extends Resource
{
    protected static ?string $model = Permintaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Permintaan Barang';
    protected static ?string $slug = 'permintaan';
    protected static ?string $navigationGroup = 'Menu';

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if ($user->hasRole('User')) {
            return (string) Permintaan::where('user_id', $user->id)
                ->where('status', 'Menunggu')
                ->count();
        }

        return (string) Permintaan::where('status', 'Menunggu')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')
                ->options([
                    'Menunggu' => 'Menunggu',
                    'Disetujui' => 'Disetujui',
                    'Ditolak' => 'Ditolak',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Nama Lengkap')->searchable(),
                TextColumn::make('user.unit.name')->label('Nama Unit')->searchable(),
                TextColumn::make('total')->label('Total Harga')->money('IDR'),
                TextColumn::make('total_barang')
                    ->label('Total Barang')
                    ->getStateUsing(fn($record) => $record->items->sum('jumlah'))
                    ->alignCenter(),
                TextColumn::make('created_at')->label('Tanggal Permintaan')->dateTime()->alignCenter(),
                TextColumn::make('status')->badge()->alignCenter()
                    ->color(fn($state) => match ($state) {
                        'Menunggu' => 'gray',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('download_pdf')
                    ->label('Unduh PDF')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('permintaan.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => Auth::user()->hasRole(roles: 'User') && $record->status === 'Disetujui'),
                Action::make('lihat-detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->form([
                        Section::make('Detail Barang')
                            ->schema([
                                Repeater::make('items')
                                    ->label('Daftar Barang')
                                    ->default(function ($record) {
                                        return $record->items->map(function ($item) {
                                            return [
                                                'nama_barang' => $item->nama_barang,
                                                'jumlah' => $item->jumlah,
                                                'subtotal' => 'Rp' . number_format($item->subtotal, 0, ',', '.'),
                                            ];
                                        })->toArray();
                                    })
                                    ->schema([
                                        TextInput::make('nama_barang')
                                            ->label('Barang')
                                            ->disabled(),

                                        TextInput::make('jumlah')
                                            ->label('Jumlah')
                                            ->disabled(),
                                        TextInput::make('subtotal')
                                            ->label('Subtotal (Rp)')
                                            ->disabled(),
                                    ])
                                    ->columns(3)
                                    ->disabled()
                                    ->dehydrated(false),

                                Placeholder::make('total')
                                    ->label('')
                                    ->content(function ($record) {
                                        $total = $record->items->sum('subtotal');
                                        return 'Total Harga : Rp' . number_format($total, 0, ',', '.');
                                    })
                                    ->extraAttributes([
                                        'class' => 'text-right font-semibold text-red-700 text-lg',
                                    ]),

                                Select::make('status')
                                    ->name('status')
                                    ->label('Ubah Status Permintaan')
                                    ->options([
                                        'Menunggu' => 'Menunggu',
                                        'Disetujui' => 'Disetujui',
                                        'Ditolak' => 'Ditolak',
                                    ])
                                    ->default(fn($record) => $record->status)
                                    ->visible(fn() => Auth::user()?->hasRole('Admin')),
                            ])
                    ])
                    ->visible(fn() => Auth::user()?->hasRole('Admin'))
                    ->action(function (array $data, $record): void {
                        $statusLama = $record->status;
                        $statusBaru = $data['status'];

                        if ($statusLama === 'Disetujui' && $statusBaru !== 'Disetujui') {
                            foreach ($record->items as $item) {
                                $barang = Barang::where('nama', $item->nama_barang)
                                    ->where('periode_tahun', $record->periode_tahun)
                                    ->first();
                                if ($barang) {
                                    $barang->increment('sisa', $item->jumlah);
                                    LogBarangMasuk::create([
                                        'kode_barang' => $barang->kode,
                                        'unit_kerja_id' => $record->user->unit_id,
                                        'jumlah' => $item->jumlah,
                                        'keterangan' => 'Barang masuk!',
                                        'periode_tahun' => $record->periode_tahun,
                                    ]);
                                }
                            }
                        }

                        if ($statusBaru === 'Disetujui' && $statusLama !== 'Disetujui') {
                            foreach ($record->items as $item) {
                                $barang = Barang::where('nama', $item->nama_barang)
                                    ->where('periode_tahun', $record->periode_tahun)
                                    ->first();
                                if ($barang) {
                                    $sisaSebelum = $barang->sisa;
                                    $barang->decrement('sisa', $item->jumlah);

                                    LogBarangKeluar::create([
                                        'kode_barang'       => $barang->kode,
                                        'unit_kerja_id'     => $record->user->unit_id ?? null,
                                        'jumlah'            => $item->jumlah,
                                        'user_id'           => $record->user_id,
                                        'sisa_stok_saat_itu'=> $sisaSebelum,
                                        'keterangan'        => 'Barang keluar!',
                                        'periode_tahun'     => $record->periode_tahun,
                                    ]);
                                }
                            }
                        }

                        $record->update([
                            'status' => $statusBaru,
                        ]);

                        LogPermintaan::create([
                            'permintaan_id' => $record->id,
                            'status_lama' => $statusLama,
                            'status_baru' => $statusBaru,
                            'user_id' => Auth::id(),
                            'keterangan' => 'Status diubah via halaman admin.',
                            'periode_tahun' => $record->periode_tahun,
                        ]);

                        Notification::make()
                            ->title('Status Diperbarui')
                            ->body('Status permintaan diubah menjadi: ' . $statusBaru)
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermintaans::route('/'),
            // 'edit' => Pages\EditPermintaan::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
