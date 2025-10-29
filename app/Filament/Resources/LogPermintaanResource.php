<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogPermintaan;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LogPermintaanResource\Pages;

class LogPermintaanResource extends Resource
{
    protected static ?string $model = LogPermintaan::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Menu Khusus';

    public static function getNavigationBadge(): ?string
    {
        return (string) LogPermintaan::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'permintaan.user']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status_lama')
                    ->label('Aktivitas')
                    ->alignCenter()
                    ->formatStateUsing(function ($state, $record) {
                        $userName = $record->user?->name ?? 'User tidak diketahui';
                        $pemintaName = $record->permintaan?->user?->name ?? 'Pengguna tidak diketahui';
                        $statusLama = $record->status_lama ?? '-';
                        $statusBaru = $record->status_baru ?? '-';

                        return "{$userName} mengubah permintaan {$pemintaName} dari {$statusLama} menjadi {$statusBaru}";
                    })
                    ->extraAttributes([
                        'class' => 'text-sm text-gray-700',
                    ])
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->searchable()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogPermintaans::route('/'),
            'create' => Pages\CreateLogPermintaan::route('/create'),
            'edit' => Pages\EditLogPermintaan::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Admin');
    }
}
