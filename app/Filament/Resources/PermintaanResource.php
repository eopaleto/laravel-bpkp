<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Permintaan;
use Filament\Tables\Table;
use App\Models\PermintaanItems;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\PermintaanResource\Pages;

class PermintaanResource extends Resource
{
    protected static ?string $model = Permintaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Permintaan Barang';
    protected static ?string $slug = 'permintaan';
    protected static ?string $navigationGroup = 'Menu';

    public static function getNavigationBadge(): ?string
    {
        return (string) Permintaan::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')
                ->options([
                    'Menunggu' => 'Menunggu',
                    'Disetujui' => 'Disetujui',
                    'Ditolak' => 'Ditolak',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Nama Lengkap')->searchable(),
                TextColumn::make('total')->label('Total Harga')->money('IDR'),
                TextColumn::make('total_barang')
                    ->label('Total Barang')
                    ->getStateUsing(fn($record) => $record->items->sum('jumlah'))
                    ->alignCenter(),
                TextColumn::make('created_at')->label('Tanggal Permintaan')->dateTime()->alignCenter(),
                TextColumn::make('status')->badge()->alignCenter()
                    ->color(fn($state) => match ($state) {
                        'Menunggu' => 'gray',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('download_pdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('permintaan.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => auth()->user()->hasRole('User') && $record->status === 'Disetujui'),
                ViewAction::make()
                    ->label('Lihat Detail')
                    ->form([
                        Section::make('Detail Barang')
                            ->schema([
                                Repeater::make('items')
                                    ->label('Daftar Barang')
                                    ->relationship('items')
                                    ->schema([
                                        TextInput::make('nama_barang')
                                            ->label('Barang')
                                            ->disabled(),

                                        TextInput::make('jumlah')
                                            ->label('Jumlah')
                                            ->disabled(),

                                        TextInput::make('subtotal')
                                            ->label('Subtotal (Rp)')
                                            ->disabled()
                                            ->formatStateUsing(fn($state) => 'Rp' . number_format($state, 0, ',', '.')),
                                    ])
                                    ->columns(3)
                                    ->disabled(),
                            ]),
                    ])
                    ->visible(fn() => auth()->user()?->hasRole('Admin')),

                EditAction::make()
                    ->label('Ubah Status')
                    ->visible(fn() => auth()->user()?->hasRole('Admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListPermintaans::route('/'),
            'edit' => Pages\EditPermintaan::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
