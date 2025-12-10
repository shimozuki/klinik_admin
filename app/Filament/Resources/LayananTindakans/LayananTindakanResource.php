<?php

namespace App\Filament\Resources\LayananTindakans;

use App\Filament\Resources\LayananTindakans\Pages\CreateLayananTindakan;
use App\Filament\Resources\LayananTindakans\Pages\EditLayananTindakan;
use App\Filament\Resources\LayananTindakans\Pages\ListLayananTindakans;
use App\Filament\Resources\LayananTindakans\Schemas\LayananTindakanForm;
use App\Filament\Resources\LayananTindakans\Tables\LayananTindakansTable;
use App\Models\LayananTindakan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LayananTindakanResource extends Resource
{
    protected static ?string $model = LayananTindakan::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-clipboard-document-check';

    protected static ?string $navigationLabel = 'Layanan Tindakan';

    protected static ?string $modelLabel = 'Layanan Tindakan';

    protected static ?string $pluralModelLabel = 'Layanan Tindakan';

    protected static ?int $navigationSort = 4;

    // protected static ?string $navigationGroup = 'Master Data';

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return LayananTindakanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LayananTindakansTable::configure($table);
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
            'index' => ListLayananTindakans::route('/'),
            'create' => CreateLayananTindakan::route('/create'),
            'edit' => EditLayananTindakan::route('/{record}/edit'),
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
        return 'Layanan Aktif';
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->kode . ' - ' . $record->nama;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['kode', 'nama', 'deskripsi'];
    }
}
