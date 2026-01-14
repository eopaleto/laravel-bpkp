<?php

namespace App\Filament\Resources\BarangResource\Pages;

use Filament\Actions;
use App\Models\Barang;
use App\Models\LogBarangMasuk;
use App\Filament\Resources\BarangResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    private function getPeriodeTahun(): ?int
    {
        return session('periode_tahun') ?: (Auth::check() ? Auth::user()->periode_tahun : null);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['periode_tahun'] = $this->getPeriodeTahun();

        $this->validate([
            'data.kode' => [
                function ($attribute, $value, $fail) use ($data) {
                    if (Barang::where('kode', $value)
                        ->where('periode_tahun', $data['periode_tahun'])
                        ->exists()
                    ) {
                        $fail('Kode barang ini sudah digunakan!');
                    }
                },
            ],
        ]);

        return $data;
    }

    protected function afterCreate(): void
    {
        LogBarangMasuk::create([
            'kode_barang' => $this->record->kode,
            'jumlah' => $this->record->sisa ?? 0,
            'keterangan' => 'Barang baru ditambahkan!',
            'periode_tahun' => $this->getPeriodeTahun(),
        ]);
    }
}
