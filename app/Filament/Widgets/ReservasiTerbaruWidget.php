<?php

namespace App\Filament\Widgets;

use App\Models\Reservasi;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ReservasiTerbaruWidget extends BaseWidget
{
    protected static ?string $heading = 'Reservasi Terbaru';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    protected function getTableQuery(): Builder
    {
        return Reservasi::query()
            ->with(['pasien.user', 'layanan'])
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nomor_reservasi')
                ->label('Kode'),

            Tables\Columns\TextColumn::make('pasien.user.name')
                ->label('Pasien')
                ->limit(20),

            Tables\Columns\TextColumn::make('layanan.nama')
                ->label('Layanan'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Tanggal')
                ->dateTime('d M Y'),

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'selesai',
                    'danger'  => 'dibatalkan',
                ]),
        ];
    }
}
