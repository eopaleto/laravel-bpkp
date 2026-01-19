<?php

namespace App\Filament\Resources\KartuGudangResource\Pages;

use App\Filament\Resources\KartuGudangResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;

class ListKartuGudangs extends Page
{
    protected static string $resource = KartuGudangResource::class;

    protected static string $view = 'filament.pages.kartu-gudang-page';

    protected function getHeaderActions(): array
    {
        return [];
    }
}

