<?php

namespace App\Filament\Resources\RekamMedis;

use App\Filament\Resources\RekamMedis\Pages\CreateRekamMedis;
use App\Filament\Resources\RekamMedis\Pages\EditRekamMedis;
use App\Filament\Resources\RekamMedis\Pages\ListRekamMedis;
use App\Filament\Resources\RekamMedis\Pages\ViewRekamMedis;
use App\Filament\Resources\RekamMedis\Schemas\RekamMedisForm;
use App\Filament\Resources\RekamMedis\Tables\RekamMedisTable;
use App\Models\RekamMedis as ModelsRekamMedis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RekamMedisResource extends Resource
{
    protected static ?string $model = ModelsRekamMedis::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-document-text';

    protected static ?string $navigationLabel = 'Rekam Medis';

    protected static ?string $modelLabel = 'Rekam Medis';

    protected static ?string $pluralModelLabel = 'Rekam Medis';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return 'Medis';
    }

    protected static ?string $recordTitleAttribute = 'nomor_rekam';

    public static function form(Schema $schema): Schema
    {
        return RekamMedisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RekamMedisTable::configure($table);
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
            'index' => ListRekamMedis::route('/'),
            'create' => CreateRekamMedis::route('/create'),
            'view' => ViewRekamMedis::route('/{record}'),
            'edit' => EditRekamMedis::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Rekam Medis Hari Ini';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nomor_rekam . ' - ' . $record->pasien->user->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nomor_rekam', 'pasien.user.name', 'diagnosis'];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user->hasRole('dokter')) {
            $query->where('dokter_id', $user->id);
        }

        return $query;
    }
}
