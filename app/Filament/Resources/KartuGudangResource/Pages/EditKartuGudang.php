<?php

namespace App\Filament\Resources\KartuGudangResource\Pages;

use App\Filament\Resources\KartuGudangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKartuGudang extends EditRecord
{
    protected static string $resource = KartuGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
