<?php

namespace App\Filament\Resources\KartuGudangResource\Pages;

use App\Filament\Resources\KartuGudangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKartuGudangs extends ListRecords
{
    protected static string $resource = KartuGudangResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

