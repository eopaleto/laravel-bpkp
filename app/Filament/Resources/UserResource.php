<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users Management';
    protected static ?string $navigationGroup = 'Menu Khusus';

    public static function getNavigationBadge(): ?string
    {
        return (string) User::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Pengguna')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->label('Nama Lengkap'),
                            TextInput::make('email')
                                ->email()
                                ->required(),
                            TextInput::make('password')
                                ->password()
                                ->label('Password')
                                ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                                ->dehydrated(fn($state) => filled($state))
                                ->required(fn(string $context) => $context === 'create'),
                            Select::make('unit_id')
                                ->label('Unit Kerja')
                                ->relationship('unit', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('roles')
                                ->label('Role Pengguna')
                                ->relationship('roles', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('unit.name')
                    ->label('Unit Kerja')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->searchable()
                    ->color(fn(string $state) => match ($state) {
                        'Admin' => 'success',
                        'User' => 'primary',
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Admin');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
