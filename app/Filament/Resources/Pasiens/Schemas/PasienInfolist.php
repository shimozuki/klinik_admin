<?php

namespace App\Filament\Resources\Pasiens\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PasienInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // IDENTITAS
            TextEntry::make('user.name')
                ->label('Nama')
                ->placeholder('-'),

            TextEntry::make('nomor_rekam_medis')
                ->label('Nomor Rekam Medis')
                ->placeholder('-'),

            TextEntry::make('nik')
                ->label('NIK')
                ->placeholder('-'),

            TextEntry::make('tanggal_lahir')
                ->label('Tanggal Lahir')
                ->date()
                ->placeholder('-'),

            TextEntry::make('jenis_kelamin')
                ->label('Jenis Kelamin')
                ->badge()
                ->placeholder('-'),

            // ALAMAT (full width)
            TextEntry::make('alamat')
                ->label('Alamat')
                ->columnSpanFull()
                ->placeholder('-'),

            // KONTAK
            TextEntry::make('telepon')
                ->label('Telepon')
                ->placeholder('-'),

            TextEntry::make('kontak_darurat')
                ->label('Kontak Darurat')
                ->placeholder('-'),

            TextEntry::make('telepon_darurat')
                ->label('Telepon Darurat')
                ->placeholder('-'),

            // RIWAYAT KESEHATAN
            TextEntry::make('golongan_darah')
                ->label('Golongan Darah')
                ->placeholder('-'),

            TextEntry::make('alergi')
                ->label('Alergi')
                ->columnSpanFull()
                ->placeholder('-'),

            // METADATA (timestamps)
            TextEntry::make('created_at')
                ->label('Dibuat')
                ->dateTime()
                ->placeholder('-'),

            TextEntry::make('updated_at')
                ->label('Diubah')
                ->dateTime()
                ->placeholder('-'),
        ]);
    }
}
