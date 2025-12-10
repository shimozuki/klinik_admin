<?php

namespace App\Filament\Resources\RekamMedis\Pages;

use App\Filament\Resources\RekamMedis\RekamMedisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRekamMedis extends ViewRecord
{
    protected static string $resource = RekamMedisResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (auth()->user()->hasRole('dokter') && $this->record->dokter_id === auth()->id()) {
            $actions[] = Actions\EditAction::make();
        }

        return $actions;
    }
}
