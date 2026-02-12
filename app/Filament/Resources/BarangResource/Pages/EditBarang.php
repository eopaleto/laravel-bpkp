<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Models\Barang;
use App\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class EditBarang extends EditRecord
{
    protected static string $resource = BarangResource::class;
    
    private function getPeriodeTahun(): ?int
    {
        return session('periode_tahun') ?: (Auth::check() ? Auth::user()->periode_tahun : null);
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Skip validasi kode jika kode tidak ada dalam data (karena di-disable untuk admin)
        if (isset($data['kode'])) {
            // Validasi kode unik per periode (exclude record saat ini)
            $exists = Barang::where('kode', $data['kode'])
                ->where('periode_tahun', $this->record->periode_tahun)
                ->where('kode', '!=', $this->record->kode)
                ->exists();
            
            if ($exists) {
                throw ValidationException::withMessages([
                    'kode' => 'Kode barang ini sudah digunakan!',
                ]);
            }
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

