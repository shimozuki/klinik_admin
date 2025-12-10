<?php

// app/Filament/Resources/Pasiens/PasienResource.php

namespace App\Filament\Resources\Pasiens;

use App\Filament\Resources\Pasiens\Pages\CreatePasien;
use App\Filament\Resources\Pasiens\Pages\EditPasien;
use App\Filament\Resources\Pasiens\Pages\ListPasiens;
use App\Filament\Resources\Pasiens\Pages\ViewPasien;
use App\Filament\Resources\Pasiens\Schemas\PasienForm;
use App\Filament\Resources\Pasiens\Schemas\PasienInfolist;
use App\Filament\Resources\Pasiens\Tables\PasiensTable;
use App\Models\Pasien;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-o-users';


    protected static ?string $navigationLabel = 'Pasien';

    protected static ?string $modelLabel = 'Pasien';

    protected static ?string $pluralModelLabel = 'Pasien';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nomor_rekam_medis';

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen';
    }

    public static function form(Schema $schema): Schema
    {
        return PasienForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PasienInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PasiensTable::configure($table);
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
            'index' => ListPasiens::route('/'),
            'create' => CreatePasien::route('/create'),
            'edit' => EditPasien::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
