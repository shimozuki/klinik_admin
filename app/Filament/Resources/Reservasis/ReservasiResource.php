<?php

namespace App\Filament\Resources\Reservasis;

use App\Filament\Resources\Reservasis\Pages\CreateReservasi;
use App\Filament\Resources\Reservasis\Pages\EditReservasi;
use App\Filament\Resources\Reservasis\Pages\ListReservasis;
use App\Filament\Resources\Reservasis\Schemas\ReservasiForm;
use App\Filament\Resources\Reservasis\Tables\ReservasisTable;
use App\Models\Reservasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ReservasiResource extends Resource
{
    protected static ?string $model = Reservasi::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?string $navigationLabel = 'Reservasi';

    protected static ?string $modelLabel = 'Reservasi';

    protected static ?string $pluralModelLabel = 'Reservasi';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nomor_reservasi';

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return ReservasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReservasisTable::configure($table);
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
            'index' => ListReservasis::route('/'),
            'create' => CreateReservasi::route('/create'),
            'edit' => EditReservasi::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'menunggu')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', 'menunggu')->count();
        return $count > 0 ? 'warning' : 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Reservasi Menunggu Konfirmasi';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nomor_reservasi . ' - ' . $record->pasien->user->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nomor_reservasi', 'pasien.user.name', 'dokter.name'];
    }
}
