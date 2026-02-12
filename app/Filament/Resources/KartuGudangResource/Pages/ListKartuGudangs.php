<?php

namespace App\Filament\Resources\KartuGudangResource\Pages;

use App\Filament\Resources\KartuGudangResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ListKartuGudangs extends Page
{
    protected static string $resource = KartuGudangResource::class;

    protected static string $view = 'filament.resources.kartu-gudang-resource.pages.list-kartu-gudangs';

    protected function getHeaderActions(): array
    {
        return [];
    }
}


