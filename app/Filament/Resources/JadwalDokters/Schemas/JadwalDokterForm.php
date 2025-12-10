<?php

namespace App\Filament\Resources\JadwalDokters\Schemas;

use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JadwalDokterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('👨‍⚕️ Informasi Dokter')
                    ->description('Pilih dokter yang akan berpraktek')
                    ->schema([
                        Forms\Components\Select::make('dokter_id')
                            ->label('Nama Dokter')
                            ->options(User::role('dokter')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-m-user-circle')
                            ->helperText('Pilih dokter yang akan menjalankan praktek')
                            ->placeholder('Cari dan pilih dokter...')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(false),

                // Card 2: Waktu Praktek
                Section::make('📅 Jadwal Praktek')
                    ->description('Tentukan hari dan jam praktek dokter')
                    ->schema([
                        Forms\Components\Select::make('hari')
                            ->label('Hari Praktek')
                            ->options([
                                'Senin' => '📘 Senin',
                                'Selasa' => '📗 Selasa',
                                'Rabu' => '📙 Rabu',
                                'Kamis' => '📕 Kamis',
                                'Jumat' => '📔 Jumat',
                                'Sabtu' => '📓 Sabtu',
                                'Minggu' => '📒 Minggu',
                            ])
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-m-calendar')
                            ->prefixIconColor('primary')
                            ->placeholder('Pilih hari praktek')
                            ->helperText('Pilih hari ketika dokter akan berpraktek')
                            ->columnSpanFull(),

                        Forms\Components\TimePicker::make('jam_mulai')
                            ->label('Jam Mulai')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->displayFormat('H:i')
                            ->prefixIcon('heroicon-m-clock')
                            ->prefixIconColor('success')
                            ->placeholder('00:00')
                            ->helperText('Contoh: 08:00'),

                        Forms\Components\TimePicker::make('jam_selesai')
                            ->label('Jam Selesai')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->displayFormat('H:i')
                            ->after('jam_mulai')
                            ->prefixIcon('heroicon-m-clock')
                            ->prefixIconColor('danger')
                            ->placeholder('00:00')
                            ->helperText('Contoh: 12:00'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                // Card 3: Kuota & Status
                Section::make('⚙️ Pengaturan Jadwal')
                    ->description('Atur kuota pasien dan status jadwal')
                    ->schema([
                        Forms\Components\TextInput::make('kuota')
                            ->label('Kuota Pasien per Hari')
                            ->numeric()
                            ->default(10)
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('pasien')
                            ->prefixIcon('heroicon-m-users')
                            ->prefixIconColor('warning')
                            ->helperText('Jumlah maksimal pasien yang dapat dilayani per hari (1-100)')
                            ->placeholder('10'),

                        Forms\Components\Toggle::make('status_aktif')
                            ->label('Status Jadwal')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-x-circle')
                            ->helperText('Aktifkan jadwal ini agar dapat digunakan untuk pendaftaran')
                            ->inline(false),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
