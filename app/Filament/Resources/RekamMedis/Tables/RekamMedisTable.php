<?php

namespace App\Filament\Resources\RekamMedis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions;

class RekamMedisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_rekam')
                    ->label('No. Rekam Medis')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor rekam medis disalin!')
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('pasien.user.name')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dokter.name')
                    ->label('Dokter Pemeriksa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_pemeriksaan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('biaya')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('reservasi.nomor_reservasi')
                    ->label('No. Reservasi')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->default('—')
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
            ])
            ->recordActions([
                ViewAction::make(),

                EditAction::make()
                    ->visible(
                        fn($record) =>
                        auth()->user()->hasRole('dokter') &&
                            $record->dokter_id === auth()->id()
                    ),

                Actions\Action::make('cetak_pdf')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn($record) => route('rekam-medis.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(
                        fn($record) =>
                        auth()->user()->hasRole('admin') ||
                            (
                                auth()->user()->hasRole('dokter') &&
                                $record->dokter_id === auth()->id()
                            )
                    ),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('dokter')),
                ]),
            ])
            ->defaultSort('tanggal_pemeriksaan', 'desc');
    }
}
