<?php

namespace App\Filament\Resources\LogBarangMasukResource\Pages;

use App\Filament\Resources\LogBarangMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogBarangMasuks extends ListRecords
{
    protected static string $resource = LogBarangMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
