<?php

namespace App\Filament\Resources\PermintaanResource\Pages;

use App\Filament\Resources\PermintaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPermintaan extends EditRecord
{
    protected static string $resource = PermintaanResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(Auth::user()?->hasRole('Admin'), 403);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
