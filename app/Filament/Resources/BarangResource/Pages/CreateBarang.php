<?php

namespace App\Filament\Resources\BarangResource\Pages;

use Filament\Actions;
use App\Models\LogBarangMasuk;
use App\Filament\Resources\BarangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;
    
        protected function afterCreate(): void
    {
        LogBarangMasuk::create([
            'kode_barang' => $this->record->kode, 
            'jumlah' => $this->record->sisa ?? 0,
            'keterangan' => 'Barang baru ditambahkan!',
        ]);
    }
}
