<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopBarang;
use App\Filament\Widgets\TopUnit;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('SuperAdmin');
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'xl' => 3,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
            TopBarang::class,
            TopUnit::class,
            StatsOverview::class,
        ];
    }
}
