<?php

namespace App\Filament\Resources\LogBarangKeluarResource\Pages;

use App\Filament\Resources\LogBarangKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogBarangKeluar extends EditRecord
{
    protected static string $resource = LogBarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
