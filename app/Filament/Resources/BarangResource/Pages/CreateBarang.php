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
        $periodeTahun = $this->getPeriodeTahun();
        $data['periode_tahun'] = $periodeTahun;

        // Auto-generate kode jika kosong
        if (empty($data['kode'])) {
            $lastBarang = Barang::where('periode_tahun', $periodeTahun)
                ->orderBy('kode', 'desc')
                ->first();
            
            if ($lastBarang) {
                // Extract number from last kode
                preg_match('/(\d+)/', $lastBarang->kode, $matches);
                $lastNumber = $matches[1] ?? 0;
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $data['kode'] = 'BRG' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        } else {
            // Validasi kode unik per periode
            if (Barang::where('kode', $data['kode'])
                ->where('periode_tahun', $periodeTahun)
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'kode' => 'Kode barang ini sudah digunakan!',
                ]);
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = Auth::user();
        LogBarangMasuk::create([
            'kode_barang' => $this->record->kode,
            'unit_kerja_id' => $user?->unit_id,
            'jumlah' => $this->record->sisa ?? 0,
            'keterangan' => 'Barang baru ditambahkan!',
            'periode_tahun' => $this->getPeriodeTahun(),
        ]);
    }
}

