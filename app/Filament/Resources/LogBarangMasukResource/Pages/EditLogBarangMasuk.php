<?php

namespace App\Filament\Resources\LogBarangMasukResource\Pages;

use App\Filament\Resources\LogBarangMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogBarangMasuk extends EditRecord
{
    protected static string $resource = LogBarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
