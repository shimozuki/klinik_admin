<?php

namespace App\Filament\Resources\KonsultasiOnline\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KonsultasiOnlineTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nomor_konsultasi')
                    ->label('No. Konsultasi')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),

                TextColumn::make('pasien.user.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dokter.name')
                    ->label('Dokter')
                    ->searchable()
                    ->sortable()
                    ->default('Belum dipilih'),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'menunggu' => 'warning',
                        'berlangsung' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray'
                    })
                    ->sortable(),

                TextColumn::make('biaya_konsultasi')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('dimulai_pada')
                    ->label('Mulai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('selesai_pada')
                    ->label('Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('catatan_dokter')
                    ->label('Catatan Dokter')
                    ->limit(40)
                    ->wrap()
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

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'berlangsung' => 'Berlangsung',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->multiple()
            ])

            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->visible(fn() => auth()->user()->hasRole('dokter')),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('dokter')),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
