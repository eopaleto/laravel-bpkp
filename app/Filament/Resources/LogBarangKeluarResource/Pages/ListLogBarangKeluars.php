<?php

namespace App\Filament\Resources\LogBarangKeluarResource\Pages;

use App\Filament\Resources\LogBarangKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogBarangKeluars extends ListRecords
{
    protected static string $resource = LogBarangKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
