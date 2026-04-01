<?php

namespace App\Filament\Pages;

use App\Models\Barang;
use App\Models\User;
use App\Models\KartuGudang;
use App\Models\LogBarangMasuk;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Components;

class TambahBarang extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationLabel = 'Tambah Barang';
    protected static ?string $title = 'Tambah Barang ke Gudang';
    protected static string $view = 'filament.pages.tambah-barang';
    protected static ?string $navigationGroup = 'Menu';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    private function getPeriodeTahun(): ?int
    {
        return session('periode_tahun') ?: (Auth::check() ? Auth::user()->periode_tahun : null);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make('Tambah Barang ke Gudang')
                    ->description('Pilih barang dan masukkan jumlah yang akan ditambahkan ke stok gudang')
                    ->schema([
                        Components\Select::make('barang_kode')
                            ->label('Pilih Barang')
                            ->searchable()
                            ->preload()
                            ->options(function () {
                                $periodeTahun = $this->getPeriodeTahun();
                                return Barang::where('periode_tahun', $periodeTahun)
                                    ->pluck('nama', 'kode')
                                    ->toArray();
                            })
                            ->required()
                            ->helperText('Cari dan pilih barang dari daftar'),

                        Components\TextInput::make('jumlah_tambah')
                            ->label('Jumlah Barang')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->step(1)
                            ->helperText('Masukkan jumlah barang yang akan ditambahkan'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $barangKode = $data['barang_kode'] ?? null;
        $jumlahTambah = (int) ($data['jumlah_tambah'] ?? 0);

        if (!$barangKode || $jumlahTambah <= 0) {
            Notification::make()
                ->title('Error')
                ->body('Pilih barang dan masukkan jumlah yang valid')
                ->danger()
                ->send();
            return;
        }

        $periodeTahun = $this->getPeriodeTahun();

        // Cari barang
        $barang = Barang::where('kode', $barangKode)
            ->where('periode_tahun', $periodeTahun)
            ->first();

        if (!$barang) {
            Notification::make()
                ->title('Error')
                ->body('Barang tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $user = Auth::user();

        try {
            // Update stok barang
            $sisaStokSebelumnya = $barang->sisa ?? 0;
            $newStok = $sisaStokSebelumnya + $jumlahTambah;
            
            $barang->update([
                'sisa' => $newStok,
            ]);

            // Buat log barang masuk
            LogBarangMasuk::create([
                'kode_barang' => $barangKode,
                'unit_kerja_id' => $user?->unit_id,
                'jumlah' => $jumlahTambah,
                'keterangan' => 'Barang ditambahkan ke gudang',
                'periode_tahun' => $periodeTahun,
            ]);

            // Buat kartu gudang untuk pencatatan
            KartuGudang::create([
                'kode_barang' => $barangKode,
                'nama_barang' => $barang->nama,
                'unit_kerja_id' => $user?->unit_id,
                'tanggal_keluar' => now(),
                'jumlah_keluar' => $jumlahTambah,
                'sisa_stok' => $newStok,
                'jenis' => 'barang_masuk',
                'keterangan' => 'Barang ditambahkan ke gudang',
                'periode_tahun' => $periodeTahun,
            ]);

            Notification::make()
                ->title('Sukses')
                ->body("Barang '{$barang->nama}' berhasil ditambahkan ke stok. Stok baru: {$newStok}")
                ->success()
                ->send();

            // Reset form
            $this->form->fill();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        // Hanya Admin yang bisa akses
        return $user?->hasRole('Admin');
    }
}
