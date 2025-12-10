<?php

namespace App\Filament\Resources\KonsultasiOnline;

use App\Filament\Resources\KonsultasiOnline\Pages\EditKonsultasiOnline;
use App\Filament\Resources\KonsultasiOnline\Pages\ListKonsultasiOnlines;
use App\Filament\Resources\KonsultasiOnline\Pages\ViewKonsultasiOnline;
use App\Filament\Resources\KonsultasiOnline\Schemas\KonsultasiOnlineForm;
use App\Filament\Resources\KonsultasiOnline\Schemas\KonsultasiOnlineInfolist;
use App\Filament\Resources\KonsultasiOnline\Tables\KonsultasiOnlineTable;
use App\Models\KonsultasiOnline;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class KonsultasiOnlineResource extends Resource
{
    protected static ?string $model = KonsultasiOnline::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Konsultasi Online';

    protected static ?string $modelLabel = 'Konsultasi Online';

    protected static ?string $pluralModelLabel = 'Konsultasi Online';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nomor_konsultasi';

    public static function getNavigationGroup(): ?string
    {
        return 'Layanan Medis';
    }

    public static function form(Schema $schema): Schema
    {
        return KonsultasiOnlineForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return KonsultasiOnlineInfolist::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return KonsultasiOnlineTable::configure($table);
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
            'index' => ListKonsultasiOnlines::route('/'),
            'view' => ViewKonsultasiOnline::route('/{record}'),
            'edit' => EditKonsultasiOnline::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'menunggu')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Konsultasi yang menunggu';
    }
}
