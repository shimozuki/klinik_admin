<?php

namespace App\Filament\Resources\KonsultasiOnline\Pages;

use App\Filament\Resources\KonsultasiOnline\KonsultasiOnlineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKonsultasiOnlines extends ListRecords
{
    protected static string $resource = KonsultasiOnlineResource::class;

    protected function getHeaderActions(): array
    {
        // Jika hanya dokter yang boleh buat konsultasi baru
        if (auth()->user()->hasRole('dokter')) {
            return [
                Actions\CreateAction::make()
                    ->label('Buat Konsultasi')
                    ->icon('heroicon-o-plus'),
            ];
        }

        // Default: selain dokter tidak boleh buat
        return [];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn() => $this->getModel()::count())
                ->badgeColor('gray'),

            'menunggu' => Tab::make('Menunggu')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'menunggu'))
                ->badge(fn() => $this->getModel()::where('status', 'menunggu')->count())
                ->badgeColor('warning')
                ->icon('heroicon-m-clock'),

            'berlangsung' => Tab::make('Berlangsung')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'berlangsung'))
                ->badge(fn() => $this->getModel()::where('status', 'berlangsung')->count())
                ->badgeColor('info')
                ->icon('heroicon-m-chat-bubble-left-right'),

            'selesai' => Tab::make('Selesai')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'selesai'))
                ->badge(fn() => $this->getModel()::where('status', 'selesai')->count())
                ->badgeColor('success')
                ->icon('heroicon-m-check-circle'),

            'dibatalkan' => Tab::make('Dibatalkan')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'dibatalkan'))
                ->badge(fn() => $this->getModel()::where('status', 'dibatalkan')->count())
                ->badgeColor('danger')
                ->icon('heroicon-m-x-circle'),
        ];
    }
}
