<?php

namespace App\Filament\Resources\LogPermintaanResource\Pages;

use App\Filament\Resources\LogPermintaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogPermintaans extends ListRecords
{
    protected static string $resource = LogPermintaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
