<?php

namespace App\Filament\Resources\JadwalDokters;

use App\Filament\Resources\JadwalDokters\Pages\CreateJadwalDokter;
use App\Filament\Resources\JadwalDokters\Pages\EditJadwalDokter;
use App\Filament\Resources\JadwalDokters\Pages\ListJadwalDokters;
use App\Filament\Resources\JadwalDokters\Schemas\JadwalDokterForm;
use App\Filament\Resources\JadwalDokters\Tables\JadwalDoktersTable;
use App\Models\JadwalDokter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JadwalDokterResource extends Resource
{
    protected static ?string $model = JadwalDokter::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Dokter';

    protected static ?string $modelLabel = 'Jadwal Dokter';

    protected static ?string $pluralModelLabel = 'Jadwal Dokter';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen';
    }

    protected static ?string $recordTitleAttribute = 'dokter.name';

    public static function form(Schema $schema): Schema
    {
        return JadwalDokterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalDoktersTable::configure($table);
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
            'index' => ListJadwalDokters::route('/'),
            'create' => CreateJadwalDokter::route('/create'),
            'edit' => EditJadwalDokter::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_aktif', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jadwal Aktif';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->dokter->name . ' - ' . $record->hari;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['dokter.name', 'hari'];
    }
}
