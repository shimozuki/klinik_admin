<?php

namespace App\Filament\Resources\Reservasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_reservasi')
                    ->label('No. Reservasi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor reservasi disalin!')
                    ->weight('bold'),

                TextColumn::make('pasien.user.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dokter.name')
                    ->label('Dokter')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jadwal.hari')
                    ->label('Hari')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Senin' => 'info',
                        'Selasa' => 'success',
                        'Rabu' => 'warning',
                        'Kamis' => 'danger',
                        'Jumat' => 'primary',
                        'Sabtu' => 'gray',
                        'Minggo' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_reservasi')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('jam_reservasi')
                    ->label('Jam')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('nomor_antrian')
                    ->label('No. Antrian')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'dikonfirmasi' => 'success',
                        'selesai' => 'info',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'menunggu' => 'Menunggu',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->multiple(),

                SelectFilter::make('dokter_id')
                    ->label('Dokter')
                    ->relationship('dokter', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('pasien_id')
                    ->label('Pasien')
                    ->relationship('pasien.user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
